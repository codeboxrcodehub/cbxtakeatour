(function ($) {
    'use strict';

    var tourguide_instance = new tourguide.TourGuideClient({});
    var tourguide_last_post_id = 0;

    jQuery(document).ready(function ($) {
        $(document.body).attr('data-cbxtakeatour-dialog', 0);

        $('.cbxtakeatour').each(function (index, element) {
            var $element = $(element);

            var $tour_id    = Number($element.data('tour-id'));
            var $auto_start = Number($element.data('auto-start'));

            $element.on('click', function (e) {
                e.preventDefault();

                cbxtakeatour_render($element, $tour_id);
            });

            if ($auto_start) {
                cbxtakeatour_render($element, $tour_id);
            }

        });//end .cbxtakeatour

        /**
         * Render the tour
         *
         * @param $element
         * @param $tour_id
         */
        function cbxtakeatour_render($element, $tour_id) {
            var $tour_active_step = 0;
            var $data = cbxtakeatour.steps[$tour_id];
            var $steps  = cbxtakeatour.steps[$tour_id].steps;
            var $layout = cbxtakeatour.steps[$tour_id].layout;
            var $url    = cbxtakeatour.steps[$tour_id].redirect_url;

            //console.log($steps);

            $(document.body).attr('data-cbxtakeatour-dialog', $tour_id);


            const $layout_class = 'cbxtakeatour_popover_' + $layout;

            const tour_option = {
                nextLabel: cbxtakeatour.tour_label_next,
                prevLabel: cbxtakeatour.tour_label_prev,
                finishLabel: cbxtakeatour.tour_label_endtour,
                hidePrev: $data.hide_prev,
                hideNext: $data.hide_next,
                dialogAnimate: $data.dialog_animate,
                dialogPlacement: undefined,
                //dialogClass: 'cbxtakeatour_dialog_'+$tour_id,
                dialogClass: 'cbxtakeatour_dialog',
                dialogZ: 99999,
                dialogWidth: 0,
                dialogMaxWidth: 340,
                backdropClass: 'cbxtakeatour_backdrop',
                backdropColor: 'rgba(20,20,21,0.84)',
                backdropAnimate: $data.backdrop_animate,
                targetPadding: 30,
                completeOnFinish: true,
                showStepDots: $data.show_step_dots,
                stepDotsPlacement: 'footer',
                showButtons: true,
                showStepProgress: $data.show_step_progress,
                progressBar: '',
                keyboardControls: $data.keyboard_controls,
                exitOnEscape: $data.exit_on_escape,
                exitOnClickOutside: $data.exit_on_click_outside,
                autoScroll: true,
                autoScrollSmooth: true,
                autoScrollOffset: 20,
                closeButton: $data.close_button,
                rememberStep: false,
                debug: $data.dev_debug,
                steps: $steps
            };

            //var tourguide_instance = new tourguide.TourGuideClient(defaultOptions);

            //tourguide_instance.refresh();

            tourguide_instance.setOptions(tour_option);
            tourguide_instance.start('cbxtakeatour_group_'+$tour_id).then(()=>{
                //console.log('event fired: start');

                //console.log(tourguide_instance.backdrop);
                //console.log(tourguide_instance.isVisible);
                //console.log(tourguide_instance.activeStep);
                //console.log(tourguide_instance.dialog);

                $tour_active_step = tourguide_instance.activeStep;

                /*if($tour_active_step  === 0){
                    $(tourguide_instance.dialog).find('#tg-dialog-prev-btn').hide();
                }*/

                //'cbxtakeatour_dialog_'+$tour_id
                //tg-dialog cbxtakeatour_dialog_260  animate-position


                //$(tourguide_instance.dialog).attr('class', 'tg-dialog cbxtakeatour_dialog_'+$tour_id+'  animate-position');

                //$(tourguide_instance.dialog).addClass('cbxtakeatour_dialog_step_'+$tour_active_step);


                /*if(tourguide_last_post_id !== 0){
                    $(tourguide_instance.dialog).removeClass('cbxtakeatour_dialog_'+tourguide_last_post_id);

                }

                tourguide_last_post_id = $tour_id;*/

                CBXTakeatourEvents_do_action('cbxtakeatour_tour_onStart', $, $element, $tour_id, tourguide_instance);
            });

            tourguide_instance.onFinish(async ()=>{
                //console.log('event fired: onFinish');

                CBXTakeatourEvents_do_action('cbxtakeatour_tour_onEnd', $, $element, $tour_id, tourguide_instance);

                if ($url !== '') {
                    document.location.href = $url;
                    //return (new $.Deferred()).promise();
                }
            });

            tourguide_instance.onBeforeStepChange(async ()=>{
                //console.log('event fired: onBeforeStepChange');

                CBXTakeatourEvents_do_action('cbxtakeatour_tour_onBeforeStepChange', $, $element, $tour_id, tourguide_instance);
            });

            tourguide_instance.onAfterStepChange(async ()=>{
                //console.log('event fired: onAfterStepChange');

                $tour_active_step = tourguide_instance.activeStep;

                CBXTakeatourEvents_do_action('cbxtakeatour_tour_onAfterStepChange', $, $element, $tour_id, tourguide_instance);
            });

            tourguide_instance.onBeforeExit(async ()=>{
                //console.log('event fired: onBeforeExit');

                CBXTakeatourEvents_do_action('cbxtakeatour_tour_onBeforeExit', $, $element, $tour_id, tourguide_instance);
            });

            tourguide_instance.onAfterExit(async ()=>{
                //console.log('event fired: onAfterExit');

                $(document.body).attr('data-cbxtakeatour-dialog', 0);

                CBXTakeatourEvents_do_action('cbxtakeatour_tour_onAfterExit', $, $element, $tour_id, tourguide_instance);
            });
        }//end function cbxtakeatour_render
    }); //dom ready
})(jQuery);