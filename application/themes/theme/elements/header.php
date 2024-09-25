<?php defined('C5_EXECUTE') or die("Access Denied.");

$site = Config::get('concrete.site');
$themePath = $this->getThemePath();

?>

<?php $this->inc('elements/header_top.php'); ?>
<?php $s = Stack::getByName("Navigation"); $s->display(); ?>
