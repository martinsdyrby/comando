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


	// *** MOLA OBJECT

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
				});
				$("#menu").append($li);
			}

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

                    var $form = $( this ),
                        service = $form.find( 'input[name="service"]' ).val(),
                        foo = $form.find( 'input[name="foo"]' ).val(),
                        url = $form.attr( 'action' ),
                        method = $form.attr( 'method' );

                    /* Send the data using post and put the results in a div */
                    if(method.toLowerCase() == 'get') {
                        $.get( url, { service: service, foo: foo },
                          function( data ) {
                              $( "#result" ).empty().append( '<pre>' + data + '</pre>' );
                          }
                        );
                    } else {
                        $.post( url, { service: service, foo: foo },
                          function( data ) {
                              $( "#result" ).empty().append( '<pre>' + data + '</pre>' );
                          }
                        );
                    }

                });
            });
        },

        destroy: function() {

        }
    };

	// *** SETUP

	window.Comando = Comando;


})(this);


// *** INIT

$(function() {

	Zumo.log.level = 2;

	Zumo.init(window.document, "resources/zumo.xml");

	Zumo.onConfLoaded = function() {
		Comando.init();
		Zumo.goto("gettest");
	}

});