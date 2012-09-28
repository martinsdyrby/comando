<?php header('Content-Type: application/javascript'); ?>

(function(window) {


	// *** STATE MANAGERS

	var StateManagers = {

		init: function() {


			// *** CASCADE CLASS

			var Cascade = function(target, session) {
				Zumo.StateManagers.BaseIo3Manager.call(this, target, session);
				this.inDelay = 300;
				this.delay = 90;
				this.timeIn = 200;
				this.timeOut = 200;
			};

			Cascade.prototype = {

				_doIn: function() {
					var that = this;
					var $div = $("div", this.target);
					for (var i = 0; i < $div.length; i++) {
						var $thisDiv = $($div[i]);
						var callback;
						if (i == $div.length - 1) {
							callback = function() {
								that.setState(Zumo.StateManagers.STATE_ON);
							}
						}
						$thisDiv.css("display", "none");
						$thisDiv.delay(i * that.delay + that.inDelay).fadeIn(that.timeIn, callback);
					}
				},

				_doOut: function() {
					var that = this;
					var $div = $("div", this.target);
					for (var i = 0; i < $div.length; i++) {
						var $thisDiv = $($div[i]);
						var callback;
						if (i == $div.length - 1) {
							callback = function() {
								that.setState(Zumo.StateManagers.STATE_OFF);
							}
						}
						$thisDiv.delay(($div.length - 1 - i) * that.delay).fadeOut(that.timeOut * 2, callback);
					}
				}

			};


			// *** INIT

			Zumo.ObjectUtils.extend(Cascade, Zumo.StateManagers.BaseIo3Manager);
			Zumo.registerStateManager("_cascade", Cascade);


		}

	}


	// *** COMANDO OBJECT

	var Comando = {

		init: function() {

			this.initMenu();

			StateManagers.init();

			$("#header").fadeIn("fast");

			// Rollovers

			$("#menu li").mouseover(function() {
				$(this).addClass("over");
			});

			$("#menu li").mouseout(function() {
				$(this).removeClass("over");
			});

		},

		initMenu: function() {

			var pageContexts = Zumo.getPageContexts();
			for (var i = 0; i < pageContexts.length; i++) {
				var pageContext = pageContexts[i];
				var $li = $(document.createElement("li"));
				$li.text(pageContext.title);
				$li.css("display", "none");
				$li.delay((pageContexts.length - i) * 200).fadeIn("slow");
				$li.click({id: pageContext.id}, function(event) {
					Zumo.goto(event.data.id);
                    $("body").trigger("page_shift");
				});
				$("#menu").append($li);
			}

		}



	};

    Comando.Commands = {

        ajax: function(conf, params) {

            var url = conf.url,
                data = conf.data || null,
                dataType = conf.dataType || "json",
                dataWrapper = conf.dataWrapper || null,
                successEvent = conf.successEvent || "success",
                errorEvent = conf.errorEvent || "error";

            delete params._args;
            $.ajax({
                url: url,
                data: params,
                dataType: dataType,
                success: function(data) {
                    var eventData;
                    if (dataWrapper) {
                        eventData = {};
                        eventData[dataWrapper] = data;
                    } else {
                        eventData = data;
                    }
                    $("body").trigger(successEvent, eventData);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $("body").trigger(errorEvent);
                }
            });

        },

        hideResult: function () {
            $(".resultblock").fadeOut("fast");
        },

        showResult: function(params) {
            $("#result").html("<pre>"+JSON.stringify(params.result, null, 4)+"</pre>");
            $(".resultblock").fadeIn("fast");
        }
    };


    Comando.Views = {};

    Comando.Views.AjaxMediator = function(dom) {

        this.dom = dom;
        this.$dom = $(dom);
    };
    Comando.Views.AjaxMediator.prototype = {
        init: function() {
            // find form and add submit call
            var $forms = this.$dom.find('form');
            $forms.each(function(){
                $(this).submit(function (event) {
                    event.preventDefault();

                    $("body").trigger("formSubmitted");

                    var $form = $( this ),
                        event = $form.attr( 'action' ),
                        method = $form.attr( 'method' );

                    var data = {};

                    var $inputs = $(':input', $form);

                    // not sure if you wanted this, but I thought I'd add it.
                    // get an associative array of just the values.
                    var values = {};
                    $inputs.each(function() {
                        data[this.name] = $(this).val();
                    });

                    $("body").trigger(event, data);
                });
            });
        },

        destroy: function() {
            var $forms = this.$dom.find('form');
            $forms.each(function(){
                $(this).unbind("submit");
            });
        }
    };

	// *** SETUP

	window.Comando = Comando;


})(this);


// *** INIT

$(function() {

	Zumo.log.level = 10;

    Zumo.init(window.document, '?service=<?= $_REQUEST['service']?>&zumo=1');

	Zumo.onConfLoaded = function() {
		Comando.init();
        $("body").trigger("startup");
	}
});