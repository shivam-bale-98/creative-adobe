<?php
/** @var View $this */
defined('C5_EXECUTE') or die(_("Access Denied."));
/** @var HtmlHelper $htmlHelper */
$htmlHelper = Loader::helper('html');
$version = \Config::get('concrete.FILE_VERSION');
$json_file_path = 'application/config/version.json';
$data = file_get_contents($json_file_path);
$data = json_decode($data, true);
$fileversion = $data['version'];
?>
<!-- <script src="<? //php echo $this->getThemePath() . '/dist/js/vendors.min.js'; ?>"></script> -->
<!-- <script src="<? //php echo $this->getThemePath() . '/dist/js/app.min.js?v='.$version; ?>"></script> -->
<script src="<?php echo $this->getThemePath() . '/dist/js/app-'.$fileversion.'.min.js?v='.$version; ?>"></script>
<script>
    var lenis = null;
</script>
<!--comment below if you don't need maps-->
<?php
// $this->addFooterItem($htmlHelper->javascript('//maps.googleapis.com/maps/api/js?key='));
?>
<!-- Google Tag Manager (noscript) -->
<!--<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WK2TMCX" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>-->
<!-- End Google Tag Manager (noscript) -->


	</body>
</html>
