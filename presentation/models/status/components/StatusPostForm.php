<?php

namespace Presentation\Models\Status\Components;

use Core\Di\DiContainer as Di;
use Core\Session\Session;
use Core\Request\Request;
use Core\Storage\File;
use Core\View\View;
use Core\View\ViewModel;
use Core\View\Builtins\Models\CsrfToken;

class StatusPostForm extends ViewModel {

    const BODY_FORM_NAME = 'body';
    const IMAGE_FORM_NAME = 'image';
    const PRESERVED_BODY_SESSION_KEY = 'preservedBody';

    protected $template = 'status/components/status_post_form';

    public function csrfViewModel(): CsrfToken {
        return Di::get(View::class)->generateCsrfTokenViewModel();
    }

    public function bodyPreserved(): string {
        return Di::get(Session::class)->get(self::PRESERVED_BODY_SESSION_KEY, '');
    }

    public function bodyFormName(): string {
        return self::BODY_FORM_NAME;
    }

    public function imageFormName(): string {
        return self::IMAGE_FORM_NAME;
    }

    public function preserveBody($body) {
        Di::get(Session::class)->oneTimeSet(self::PRESERVED_BODY_SESSION_KEY, $body);
    }

    public function retrieveBody(): string {
        return Di::get(Request::class)->getPost(self::BODY_FORM_NAME, '');
    }

    public function retrieveImage(): ?File {
        return Di::get(Request::class)->getFile(self::IMAGE_FORM_NAME, null);
    }
}
