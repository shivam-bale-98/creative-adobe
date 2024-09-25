<?php   
namespace Concrete\Package\WhaleClearClipboard\Job;

defined('C5_EXECUTE') or die('Access denied.');

use Concrete\Core\Job\Job;
use Concrete\Core\Page\Stack\Pile\Pile;
use Concrete\Core\Page\Stack\Pile\PileContent;

class WhaleClearClipboard extends Job 
{
    public function getJobName() 
    {
        return t("Clear Clipboard");
    }
    
    public function getJobDescription() 
    {
        return t("Clear the entire clipboard");
    }
    
    public function run() 
    {
        $num = 0;
        $sp = (new Pile())->getDefault();
        $contents = $sp->getPileContentObjects('date_asc');
        foreach ($contents as $pile_content) {
            $pcID = $pile_content->getPileContentID();

            $pileContent = PileContent::get($pcID);
            $pileContent->delete();
            $num++;
        }
          return t("%d item(s) removed from the clipboard.", $num);
           return true;
    }
}
