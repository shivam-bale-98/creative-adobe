<?php
namespace Concrete\Package\Formidable\Src\Formidable\Tasks;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Command\Task\Controller\AbstractController;
use Concrete\Package\Formidable\Src\Formidable\Tasks\CleanupCommand;
use Concrete\Core\Command\Task\Runner\CommandTaskRunner;

class CleanupController extends AbstractController
{
    public function getName(): string
    {
        return t('Cleanup Formidable');
    }

    public function getDescription(): string
    {
        return t("Removes temporary files and remove results based on GDPR");
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        return new CommandTaskRunner($task, new CleanupCommand(), t('Temporary files successfully removed!'));
    }
}
