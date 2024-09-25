<?php

namespace Application\Controller\SinglePage\Dashboard\System\Files;

use Concrete\Core\Page\Controller\DashboardPageController;

class FileUploadOptions extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');
        $file_size_limit = $config->get('concrete.file_manager.file_size_limit');
        $this->set('file_size_limit', $file_size_limit);
    }

    public function save()
    {
        if ($this->token->validate('file_upload_options')) {
            $post = $this->request->request;
            $valn = $this->app->make('helper/validation/numbers');
            $config = $this->app->make('config');

            $file_size_limit = (int) $post->get('file_size_limit');
            if ($valn->integer($file_size_limit, 0, 100)) {
                $file_size_limit = (int) $file_size_limit;
            } else {
                $this->error->add(t('Invalid File Size'));
            }

            if (!$this->error->has()) {
                $config->save('concrete.file_manager.file_size_limit', $file_size_limit);
                $this->flash('success', t('File options saved.'));
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
        $this->view();
    }


}
