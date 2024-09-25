<div class="w-[100vw]  h-[100vh] bg-white flex items-center justify-center">

  <div class="flex flex-col items-center justify-center mt-[27.7rem] sm:mt-[17.6rem] text-black">
    <?php $s = Stack::getByName('breadcrumb');
    if ($s) {
      $s->display();
    } ?>
    <h2 class=" text-[4rem] mx-16 mb-5 lg:mx-0 lg:text-[8rem] md:text-[7rem] lg:mt-[10rem] mt-[5rem]">Thank you!</h2>
    <h4 class=" mb-32 mx-16 lg:mx-0 lg:mb-[9.5rem] text-center">Your enquiry has been submitted successfully</h4>
    <div class="fade-text mb-32 lg:mb-[16.5rem]">
      <a class="channeline-btn channeline-btn--arrow channeline-btn--border channeline-btn--red relative xl:mt-[4rem] mt-[2rem] " href="<?php echo View::url('/'); ?>">
        <span class="text z-2 relative">Back to the homepage</span>
        <div class="shape absolute">
          <i class="icon-right_button_arrow absolute z-2"></i>
          <span class="bg absolute inset-0 size-full"></span>
        </div>
      </a>
    </div>
  </div>
</div>