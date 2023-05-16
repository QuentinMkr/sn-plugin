(function ($) {

	$(window).on('load', function () {

		// Import d'un lot ( Respecte les paramètres de publication )
		jQuery(".snimport").click(function (obj) {
			var title = jQuery(this).attr("data-title");
			Notiflix.Report.info('L\'importation va commencer', 'L\'importation du lot : ' + title + ' est en cours. Vous pouvez continuer votre navigation pendant ce temps.', 'Ok');

            var id = jQuery(this).attr("data-id");
            
			var data = {
				'action': 'snimport',
				'id' : id
			};
			return jQuery.post(ajaxurl, data, function (response) {
				
			});

        });

		// Import d'un lot en draft
		jQuery(".snimportdraft").click(function (obj) {
			var title = jQuery(this).attr("data-title");
			Notiflix.Report.info('L\'importation va commencer', 'L\'importation du lot : ' + title + ' en brouillon est en cours. Vous pouvez continuer votre navigation pendant ce temps.', 'Ok');

            var id = jQuery(this).attr("data-id");
            
			var data = {
				'action': 'snimportdraft',
				'id' : id
			};
			return jQuery.post(ajaxurl, data, function (response) {
			});

        });

		// Import d'un lot en publish
		jQuery(".snimportpublish").click(function (obj) {
			var title = jQuery(this).attr("data-title");
			Notiflix.Report.info('L\'importation va commencer', 'L\'importation et la publication du lot : ' + title + ' sont en cours. Vous pouvez continuer votre navigation pendant ce temps.', 'Ok');

            var id = jQuery(this).attr("data-id");
            
			var data = {
				'action': 'snimportpublish',
				'id' : id
			};
			return jQuery.post(ajaxurl, data, function (response) {
			});

        });


		// Import d'un article unique ( Respecte les paramètres de publication )
		jQuery(".snimportarticle").click(function (obj) {
            var title = jQuery(this).attr("data-title");

			Notiflix.Report.info('L\'importation va commencer', 'L\'importation de l\'article :  <br>' + title + ' </b> est en cours. <br><br>Vous pouvez continuer votre navigation pendant ce temps.', 'Ok');

            var id_article = jQuery(this).attr("data-id-article");
            
			var data = {
				'action': 'snimportarticle',
				'id_article' : id_article
			};
			return jQuery.post(ajaxurl, data, function (response) {
				console.log(response)
			});

        });

		// Import d'un article en draft
		jQuery(".snimportarticledraft").click(function (obj) {
            var title = jQuery(this).attr("data-title");
			Notiflix.Report.info('L\'importation va commencer', 'L\'importation en brouillon de l\'article : ' + title + ' est en cours. Vous pouvez continuer votre navigation pendant ce temps.', 'Ok');

            var id_article = jQuery(this).attr("data-id-article");
            
			var data = {
				'action': 'snimportarticledraft',
				'id_article' : id_article
			};
			return jQuery.post(ajaxurl, data, function (response) {
				// 
			});

        });

		// Import d'un article en publish
		jQuery(".snimportarticlepublish").click(function (obj) {
			var title = jQuery(this).attr("data-title");
			Notiflix.Report.info('L\'importation va commencer', 'L\'importation et la publication de l\'article : ' + title + ' est en cours. Vous pouvez continuer votre navigation pendant ce temps.', 'Ok');

            var id_article = jQuery(this).attr("data-id-article");
            
			var data = {
				'action': 'snimportarticlepublish',
				'id_article' : id_article
			};
			return jQuery.post(ajaxurl, data, function (response) {
			});

        });

		jQuery("#setting_status_select").change(function (obj) {
			var status = jQuery("#setting_status_select").val()
			jQuery("#setting_status").val(status);
		});

		jQuery('.purchase_button').click(function (e) {
			timerInstance.start(/* config */);
			timerInstance.addEventListener('secondsUpdated', function (e) {
					$('.nx-loading-message').html(
				'Chargement... Merci de patienter. <br>' +
				timerInstance.getTimeValues().toString(),
			);
				});
				Notiflix.Loading.hourglass('Chargement ...');
			var id = jQuery(this).attr('data-id');
			var price = jQuery(this).attr('data-credits');

			var data = {
				action: 'snpurchaselot',
				id_lot: id,
				price: price,
			};


      		return jQuery.post(ajaxurl, data, function (response) {
				console.log(response);
        	Notiflix.Loading.remove();

        if (response.data.success == 1) {
          Notiflix.Report.success(
            'Achat réussi',
            'Achat du lot : ' + response.data.lot + ' réussi !',
            'Ok',
            () => {
              document.location.reload(true);
            },
          );
        } else {
          if (response.data.msg.includes('crédits')) {
            Notiflix.Confirm.show(
              "Échec de l'achat",
              response.data.msg,
              'En acheter',
              'Dommage',
              () => {
                window.open('https://www.savage-note.com/credits/', '_blank');
              },
              () => {},
              {
                titleColor: '#1E1E1E',
                okButtonBackground: '#009CE0',
              },
            );
          } else if (response.data.msg.includes('mettre à jour')){

			Notiflix.Report.warning(
				"Version obsolète",
				response.data.msg,
				'Télécharger la nouvelle version',
				() => {
				  window.open("https://app.savage-note.com/sn-plugin.zip", '_blank');
				},
			  );

		  } else {
            Notiflix.Report.failure(
              "Échec de l'achat",
              response.data.msg,
              'Ok',
              () => {
                document.location.reload(true);
              },
            );
          }
        }
      });
    	});

		var time = new Date();

		// flatpickr("#datepicker", {
		// 	altInput: true,
		// 	altFormat: "d-m-Y H:i:S",
		// 	dateFormat: "Y-m-d H:i:S",
		// 	locale: "fr",
		// 	enableTime: true,
		// 	minDate: "today",
		// 	minTime: time.getHours() + ":" + time.getMinutes(),
		// });

		$('#datepicker').Zebra_DatePicker({
			days: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
			default_position: 'above',
			format: 'd-m-Y H:i',
			lang_clear_date: 'Effacer',
			months: ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'],
			show_select_today: 'Aujourd\'hui',
			start_date: new Date()

		})

		$('#datepicker').on('click', function () {
			$('#datepicker').removeClass('sn-input-error');
			$('.sn-error-date').remove();
		})

		$('#time').on('click', function () {
			$('#time').removeClass('sn-input-error');
			$('#recurence').removeClass('sn-input-error');
			$('.sn-error-time').remove();
		})

		$('#recurence').on('click', function () {
			$('#time').removeClass('sn-input-error');
			$('#recurence').removeClass('sn-input-error');
			$('.sn-error-time').remove();
		})


		$('#sn-schedule').click(function (e) {
			
			e.preventDefault();

			const element = $('input[type="checkbox"][name="element[]"]:checked');

			if( !element.length > 0 ){

				Notiflix.Notify.init({
					position: 'right-bottom',
					clickToClose: true
				})

				Notiflix.Notify.failure('Veuillez sélectionner au moins un élément');
				
				return;
			}

			const popup = $('.sn-popup')
			const close = $('#sn-popup-close')

			popup.fadeIn(500);

			close.on('click', function(){
				popup.fadeOut();
			});

			$(document).on('click', function(event) {
				if ($(event.target).is(popup)) {
					popup.fadeOut();
				}
			});
		
		})

		$('#sn-confirm-schedule').click(function (e) {

			const date = $('#datepicker').val()

			if( date == ''  ){
				$('#datepicker').addClass('sn-input-error');
				if( $('.sn-error-date').length == 0 ){
					$('.sn-start-date').append('<span class="sn-error-message sn-error-date">Veuillez choisir une date.</span>');
				}
				return;
			}

			const time = $('#time').val()

			if( time == ''  ){
				$('#time').addClass('sn-input-error');
				$('#recurence').addClass('sn-input-error');
				if( $('.sn-error-time').length == 0 ){
					$('.sn-time').append('<span class="sn-error-message sn-error-time">Veuillez choisir une récurrence valide.</span>');
				}
				return;
			}

			const recurence = $('#recurence').val()

			if( recurence == '' || recurence == null  ){
				$('#time').addClass('sn-input-error');
				$('#recurence').addClass('sn-input-error');
				if( $('.sn-error-time').length == 0 ){
					$('.sn-time').append('<span class="sn-error-message sn-error-time">Veuillez choisir une récurrence valide.</span>');
				}
				return;
			}

			const element = $('input[type="checkbox"][name="element[]"]:checked');

			var values = element.map(function() {
				return $(this).val(); // Récupérer la valeur de chaque élément coché
			}).get();

			Notiflix.Report.info('La planification est en cours', 'L\'importation et la planification des articles est en cours. Vous pouvez continuer votre navigation pendant ce temps.', 'Ok');

			const data = {
				action: 'savage_add_scheduled_post',
				date: date,
				time: time,
				recurence: recurence,
				element: values
			}

			jQuery.post(ajaxurl, data, function (response) {

			});

			
			
		})


    });
})
(jQuery);
