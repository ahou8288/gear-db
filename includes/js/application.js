
function fooBar(){
	console.log('yo yo yo ric');
}



// Functions to execute as soon as the document is ready on every page.
$(document).ready(function() {
	// Bind click handler for back buttons.
	$('.btn-back').click(function() {
		history.back();
	});


	// Automatically refresh/initialise bootstrap tooltips on page load.
	opus.html.refresh_tooltips();

	// Automatically refresh datepickers.
	opus.html.refresh_datepickers();

	// Hide page alerts after a short while.
	opus.html.clear_messages(6000);

	//check if we are navigating away from a form. give a form a class of monitor to monitor it.
	var is_form_changed = false;
   $('.monitor :input').change(function(){
		if(!is_form_changed){
				is_form_changed = true;
				//console.log(is_form_changed);
		}
   });    
	 
	 //check all non button links.
	$('a:not(.btn)').click(function(){
		var a = this;
		if(is_form_changed && !$(a).parent().hasClass('input-group-addon')){
			bootbox.confirm('Are you sure? You haven\'t finished the form. Click OK to leave or Cancel to go back and finish your form.', function(result) {
				if(result){
					is_form_changed = false;
					window.location = a.href;
				} 
			});
			// var confirmExit = confirm('Are you sure? You haven\'t saved your changes. Click OK to leave or Cancel to go back and save your changes.');
			// if(confirmExit){
			// 	is_form_changed = false;
			// 	return true;
			// }
			// else{
			// 	return false;
			// }
			return false;
		}

	});
});

// Jquery validate method for validating dates.
jQuery.validator.addMethod("nz_date", function(value, element) {
	return this.optional(element) || (value.match(/^(0?[1-9]|[12][0-9]|3[01])[\/](0?[1-9]|1[012])[\/]\d{4}$/));
}, "* The date should be in dd/mm/yyyy format.");

// A namespace to hold our own utility functions. Put any new utility functions here, under the appropriate category.
opus = {
	// Utility functions for working with numbers.
	number: {
		// Prepends to the given number using the given string.
		'pad': function(num, width, str) {
			str = str || '0';
			num = num + '';
			return num.length >= width ? num : new Array(width - num.length + 1).join(str) + num;
		},
		// Creates a formatted currency string from the given number.
		'format_money': function(num, decimal_places, decimal_point, thousands_separator, money_symbol) {
			var decimal_places = isNaN(decimal_places = Math.abs(decimal_places)) ? 2 : decimal_places,
					decimal_point = decimal_point == undefined ? "." : decimal_point,
					thousands_separator = thousands_separator == undefined ? "," : thousands_separator,
					sign = num < 0 ? "-" : "",
					symbol = money_symbol || "$",
					i = parseInt(num = Math.abs(+num || 0).toFixed(decimal_places)) + "",
					j = (j = i.length) > 3 ? j % 3 : 0;
			return sign + symbol + (j ? i.substr(0, j) + thousands_separator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_separator) + (decimal_places ? decimal_point + Math.abs(num - i).toFixed(decimal_places).slice(2) : "");
		}
	},
	// Utility functions for working with strings.
	string: {
		// Calculates an appropriate string based on the given number of bytes. i.e 10 MB or 600 KB
		'byte_str': function(bytes) {
			var kb = bytes / 1000;
			return (kb > 1000 ? ((kb / 1000).toFixed(1) + ' MB') : (kb.toFixed(1) + ' KB'));
		}
	},
	// Utility functions for working with dates.
	date: {
		// Get the number of days in the given month. "1" means January.
		'days_in_month': function(month, year) {
			return new Date(year, month, 0).getDate();
		},
		// Format a given date string and return a default value if the date is invalid (i.e cannot be parsed by moment)
		'format_date': function(input_date, date_format, default_val) {
			var input_moment = moment(input_date);
			return (input_moment.isValid() ? input_moment.format(date_format) : (default_val || ""));
		}
	},
	// Utility functions for working with HTML, includes a few functions for generating commonly used html markup.
	html: {
		// Returns an appropriate icon for representing a boolean value.
		'boolean_icon': function(val) {
			var icon_ok = 'glyphicon-ok-3';
			var icon_remove = 'glyphicon-cancel-3';
			return '<span class="' + (val ? icon_ok : icon_remove) + '"></span>';
		},
		// Similar to PHPs nl2br(). Converts newline characters to <br> tags.
		'nl2br': function nl2br(str, is_xhtml) {
			var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
			return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
		},
		// Clears any header messages.
		'clear_messages': function(delay, container) {
      if(!container)
        container = '.page-alerts';
        
			var messages = $('.alert', container);

			if (delay) {
				setTimeout(function() {
					// Recursive action after a short delay.
					opus.html.clear_messages(null, container);
				}, delay);
			} else {
				messages.each(function(idx, alert) {
					$(alert).slideUp('fast');
				});
			}
		},
		// Display a header message
		'set_message': function(msg, msg_class, container) {
			// Clear any old messages.
			this.clear_messages(null, container);
			var message = "<div class='alert alert-" + (msg_class ? msg_class : 'success') + "'>" + msg + "<button type='button' class='close' data-dismiss='alert'>&times;</button></div>";
      if(!container)
        container = '.page-alerts';
			$(container).append(message);
			this.clear_messages(6000, container); // Clear this message after a short period.
		},
		// Refreshes any bootstrap tooltips in the current page.
		'refresh_tooltips': function() {
			$('.show-tooltip').tooltip();
			$('.show-tooltip-left').tooltip({'placement': 'left'});
			$('.show-tooltip-right').tooltip({'placement': 'right'});
			$('.show-tooltip-top').tooltip({'placement': 'top'});
			$('.show-tooltip-bottom').tooltip({'placement': 'bottom'});
		},
		// Refreshes any jquery ui datepickers in the current page.
		'refresh_datepickers': function() {
			$('.datepicker').each(function() {
				var input = $(this);
				var controls = input.parent();
				var div = $("<div class='input-group'><span class='input-group-addon'><a href='#'><i class='glyphicon glyphicon-calendar'></i></a></span></div>").insertAfter(input);
				input.prependTo(div).datepicker();
				div.find('a').click(function() {
					input.datepicker("show");
					return false;
				});
			});
		},
		// Creates a spinner object (spin.js) designed to be placed on a bootstrap .add-on input element.
		'input_spinner': function(element,config) {
			var opts = {
				lines: 11, // The number of lines to draw
				length: 3, // The length of each line
				width: 2, // The line thickness
				radius: 4, // The radius of the inner circle
				corners: 1, // Corner roundness (0..1)
				rotate: 0, // The rotation offset
				direction: 1, // 1: clockwise, -1: counterclockwise
				color: '#d2252f', // #rgb or #rrggbb or array of colors
				speed: 1.9, // Rounds per second
				trail: 100, // Afterglow percentage
				shadow: false, // Whether to render a shadow
				hwaccel: false, // Whether to use hardware acceleration
				className: 'spinner', // The CSS class to assign to the spinner
				zIndex: 2e9, // The z-index (defaults to 2000000000)
				top: 'auto', // Top position relative to parent in px
				left: 'auto' // Left position relative to parent in px
			};

			return new Spinner($.extend(true, {}, opts, config)).spin(document.getElementById(element.replace(/#/g, "")));
		},
		// Use to extend jquery validation config to use bootstrap themed elements.
		'validate_options': function(options) {
			var defaults = {
				highlight: function(element) {
					$(element).closest('.form-group').addClass('has-error');
				},
				unhighlight: function(element) {
					$(element).closest('.form-group').removeClass('has-error');
				},
				errorElement: 'span',
				errorClass: 'help-block',
				errorPlacement: function(error, element) {
					if (element.closest('.input-group').length) {
						error.insertAfter(element.closest('.input-group').parent());
					} else {
						error.insertAfter(element);
					}
				}
			}

			return $.extend({}, defaults, options);
		},
		/**
		 * Sets up a typeahead.js component on the given input field and turns it into an auto-complete. 
		 * The input field should be standalone as this function wraps an input-group around it.
		 * The contents of the dropdown are populated based on the results of an ajax call after keypress. 
		 * @param {string} element_id ID of the input element to bind to without the '#'.
		 * @param {string} url The url to send queries to.
		 * @param {function} datum_formatter An optional (but usually passed in) function that interprets the server-side data. Needs to return an object with value property at the very least.
		 * @param {int} max_results Max number of results to fetch. Defaults to 10.
		 * @param {function} on_select An optional callback that is fired upon selection of a dropdown item.
		 * @param {function} on_send An optional callback that is fired when a xhr is sent.
		 * @param {function} on_search_complete An optional callback that is fired when the xhr is completed.
		 * @param {function} on_close An optional callback that is firred when the typeahead is closed.
		 */
		'input_suggest': function(element_id, url, datum_formatter, max_results, on_select, on_send, on_search_complete, on_close) {
			max_results = max_results || 10;
			// Provide a default datum formatter if necessary. This is simply a function that interprets the server-side results. 
			datum_formatter = datum_formatter || function(result) {
				return {value: result.label, data: result};
			};
			
			// Wrap some extra markup around the input element and setup a spin.js spinner.
			var element = $('#' + element_id);
			var spinner = null;
			var spinner_id = element_id + '_spinner';
			var search_icon_id = element_id + '_icon';
			var spinner_timeout = null;
			element.wrap('<div class="input-group"></div>');
			element.before('<span id="' + spinner_id + '" class="input-group-addon"><i id="' + search_icon_id + '" class="glyphicon glyphicon-search"></i></span>');
			var search_icon = $('#' + search_icon_id);
			
			// Setup the typeahead Bloodhound lookup engine.
			var lookup_engine = new Bloodhound({
				datumTokenizer: function (datum) {
					return Bloodhound.tokenizers.whitespace(datum.value);
				},
				queryTokenizer: Bloodhound.tokenizers.whitespace,
				remote: {
					'url': site_url + url + '?term=%QUERY&max=' + max_results + '&format=json',
					'dataType': 'json',
					'cache': false,
					'ajax': {
						'beforeSend': function() {
							spinner = opus.html.input_spinner(spinner_id);
							search_icon.hide();
							
							if (on_send) {
								on_send();
							}
						},
						'complete': function() {
							if (spinner_timeout) {
								clearTimeout(spinner_timeout);
							}

							spinner_timeout = setTimeout(function() {
								spinner.stop();
								spinner = null;
								search_icon.show();
							}, 400);
							
							if (on_search_complete) {
								on_search_complete();
							}
						}
					},
					'rateLimitWait': 800,
					filter: function(data) {
						// This function formats the results. It must return an array. Elements of this array are referred as "datums".
						// Typeahead reads the "value" property of each datum and displays that in the dropdown list. If you provide a custom datum formatter, ensure each datum has a value property!
						return data.result ? $.map(data.result, datum_formatter) : [];
					}
				},
				limit: max_results
			});
			
			lookup_engine.initialize();
 
			// Finally setup the typeahead.
			var typeahead = element.typeahead(null, {
				displayKey: 'value',
				source: lookup_engine.ttAdapter(),
				limit: max_results
			}).each(function() { // Apply a small styling fix. As typeahead JS doesnt natively work with bootstrap 3...
				if ($(this).hasClass('input-lg')) {
					$(this).prev('.tt-hint').addClass('hint-lg');
				}
 
				if ($(this).hasClass('input-sm')) {
					$(this).prev('.tt-hint').addClass('hint-sm');
				}
			});
 
			if (on_close) {
				typeahead.on('typeahead:closed', on_close);
			}
 
			// Invoke the above callback to fetch the full address details when selecting an address.
			if (on_select) {
				typeahead.on('typeahead:selected', function(evt, datum) {
					on_select(datum);
				});
				typeahead.on('typeahead:autocompleted', function(evt, datum) {
					on_select(datum);
				});
			}
			
			return typeahead;
		}
	}
};


// Load all our extensions, configs and library plugins. (Self-executing anonymous function)
(function() {

	$.fn.dataTable.moment = function ( format, locale ) {
		var types = $.fn.dataTable.ext.type;
	 	
		// Add type detection
		types.detect.unshift( function ( d ) {
			return moment( d, format, locale, true ) && moment( d, format, locale, true ).isValid() ?
				'moment-'+format :
				null;
		} );
	 
		// Add sorting method - use an integer for the sorting
		types.order[ 'moment-'+format+'-pre' ] = function ( d ) {
			return moment( d, format, locale, true ).unix();
		};
	};


	// Setup the defaults for jquery datepicker.
	$.datepicker.setDefaults({
		showOtherMonths: true,
		showButtonPanel: true,
		changeMonth: true,
		changeYear: true,
		selectOtherMonths: true,
		dateFormat: "dd/mm/yy"
	});

	// Custom Knockout binding for a bootstrap popover.
	ko.bindingHandlers.bootstrapPopover = {
		init: function(element, valueAccessor, allBindingsAccessor, viewModel) {
			var options = valueAccessor();
			var defaultOptions = {html: true};
			options = $.extend(true, {}, defaultOptions, options);
			$(element).popover(options);
		}
	};

	// jQuery validation rule for performing valdation using regular expressions.
//	$.validator.addMethod("regex", function(value, element, regexp) {
//		var re = new RegExp(regexp);
//		return this.optional(element) || re.test(value);
//	}, "Incorrect format; Please check your input.");

	// Datatables extention to use bootstrap classes.
	$.extend($.fn.dataTableExt.oStdClasses, {
		"sWrapper": "dataTables_wrapper form-inline"
	});

	// Datatables API method to get paging information, required by some datatables plugins
	$.fn.dataTableExt.oApi.fnPagingInfo = function(oSettings) {
		return {
			"iStart": oSettings._iDisplayStart,
			"iEnd": oSettings.fnDisplayEnd(),
			"iLength": oSettings._iDisplayLength,
			"iTotal": oSettings.fnRecordsTotal(),
			"iFilteredTotal": oSettings.fnRecordsDisplay(),
			"iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
			"iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
		};
	};

	// Datatables extenstion for using bootstrap styled pagination controls
	$.extend($.fn.dataTableExt.oPagination, {
		"bootstrap": {
			"fnInit": function(oSettings, nPaging, fnDraw) {
				var oLang = oSettings.oLanguage.oPaginate;
				var fnClickHandler = function(e) {
					e.preventDefault();
					if (oSettings.oApi._fnPageChange(oSettings, e.data.action)) {
						fnDraw(oSettings);
					}
				};

				$(nPaging).addClass('pagination').append(
						'<ul>' +
						'<li class="prev disabled"><a href="#">&larr; ' + oLang.sPrevious + '</a></li>' +
						'<li class="next disabled"><a href="#">' + oLang.sNext + ' &rarr; </a></li>' +
						'</ul>'
						);
				var els = $('a', nPaging);
				$(els[0]).bind('click.DT', {
					action: "previous"
				}, fnClickHandler);
				$(els[1]).bind('click.DT', {
					action: "next"
				}, fnClickHandler);
			},
			"fnUpdate": function(oSettings, fnDraw) {
				var iListLength = 5;
				var oPaging = oSettings.oInstance.fnPagingInfo();
				var an = oSettings.aanFeatures.p;
				var i, j, sClass, iStart, iEnd, iHalf = Math.floor(iListLength / 2);

				if (oPaging.iTotalPages < iListLength) {
					iStart = 1;
					iEnd = oPaging.iTotalPages;
				}
				else if (oPaging.iPage <= iHalf) {
					iStart = 1;
					iEnd = iListLength;
				} else if (oPaging.iPage >= (oPaging.iTotalPages - iHalf)) {
					iStart = oPaging.iTotalPages - iListLength + 1;
					iEnd = oPaging.iTotalPages;
				} else {
					iStart = oPaging.iPage - iHalf + 1;
					iEnd = iStart + iListLength - 1;
				}

				for (i = 0, iLen = an.length; i < iLen; i++) {
					// Remove the middle elements
					$('li:gt(0)', an[i]).filter(':not(:last)').remove();

					// Add the new list items and their event handlers
					for (j = iStart; j <= iEnd; j++) {
						sClass = (j == oPaging.iPage + 1) ? 'class="active"' : '';
						$('<li ' + sClass + '><a href="#">' + j + '</a></li>')
								.insertBefore($('li:last', an[i])[0])
								.bind('click', function(e) {
									e.preventDefault();
									oSettings._iDisplayStart = (parseInt($('a', this).text(), 10) - 1) * oPaging.iLength;
									fnDraw(oSettings);
								});
					}

					// Add / remove disabled classes from the static elements
					if (oPaging.iPage === 0) {
						$('li:first', an[i]).addClass('disabled');
					} else {
						$('li:first', an[i]).removeClass('disabled');
					}

					if (oPaging.iPage === oPaging.iTotalPages - 1 || oPaging.iTotalPages === 0) {
						$('li:last', an[i]).addClass('disabled');
					} else {
						$('li:last', an[i]).removeClass('disabled');
					}
				}
			}
		}
	});

})();


