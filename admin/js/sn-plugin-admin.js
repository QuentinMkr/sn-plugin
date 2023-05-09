(function ($) {

	$(window).on('load', function () {

		var timerInstance = new easytimer.Timer();

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


    });



})
(jQuery);
