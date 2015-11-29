<?php

namespace Eventum\Controller;

use Auth;
use Misc;
use Project;
use AuthCookie;

class IndexController extends BaseController
{
    /** @var string */
    protected $tpl_name = 'index.tpl.html';

    /** @var string */
    private $url;

    public function __construct()
    {
        // do this before creating template engine
        // TODO: move this to BaseController?
        $this->checkRequirements();
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $request = $this->getRequest();

        $this->url = (string)$request->get('url');
    }

    /**
     * @inheritdoc
     */
    protected function canAccess()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function defaultAction()
    {
        $has_valid_cookie = AuthCookie::hasAuthCookie();
        $is_anon_user = Auth::isAnonUser();

        // log anonymous users out so they can use the login form
        if ($has_valid_cookie && $is_anon_user) {
            Auth::logout();
        }

        if ($has_valid_cookie && !$is_anon_user) {
            $params = array();
            if ($this->url) {
                $params['url'] = $this->url;
            }
            $this->redirect('select_project.php', $params);
        }

        if (Auth::autoRedirectToExternalLogin()) {
            $this->redirect(Auth::getExternalLoginURL());
        }
    }

    private function checkRequirements()
    {
        $errors = array();

        // check if templates_c is writable by the web server user
        if (!Misc::isWritableDirectory($dir = APP_TPL_COMPILE_PATH)) {
            $errors[] = ev_gettext('Directory "%1$s" is not writable.', $dir);

            Misc::displayRequirementErrors($errors);
            exit;
        }
    }

    /**
     * @inheritdoc
     */
    protected function prepareTemplate()
    {
        $projects = Project::getAnonymousList();
        $anonymous_post = (int)!empty($projects);

        $this->tpl->assign(
            array(
                'anonymous_post' => $anonymous_post,
                'login_url' => Auth::getExternalLoginURL(),
            )
        );
    }
}
