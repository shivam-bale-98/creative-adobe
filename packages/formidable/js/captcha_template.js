let formidableCaptchaHead = document.getElementsByTagName('head')[0];
let formidableCaptchaJs = document.createElement('script');
formidableCaptchaJs.type = 'text/javascript';
formidableCaptchaJs.src = `https://www.google.com/recaptcha/api.js?render=${formidable_captcha_site_key}`;
let initializedCaptcha = 0;

$(window).on('scroll load', function() {
    setTimeout(function() {
        //if($('.formidable').hasClass('formInview')) {
        if(!initializedCaptcha){
            formidableCaptchaHead.appendChild(formidableCaptchaJs);
            initializedCaptcha = 1;
            console.log('Captcha initialize');
        }
        //}
    },100);
});