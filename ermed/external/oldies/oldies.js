/**
 * Module Oldies
 * Based on http://vrublevsky.org/habr/oldies/oldies.zip
 */
(function() {
	if (typeof oldies != 'undefined')
		return;
	oldies = true;
	var openFlag = false, infoBox = document.createElement('div');
	infoBox.innerHTML = infoText;
	
	var 
	
	styles = {
		bar: {
			'z-index': 65535,
			'overflow': 'hidden',
			'-moz-transition': 'all 1s linear',
			'-o-transition' : 'all 1s linear',
			'-webkit-transition': 'all 1s linear',
			'background': '#ffffe1 no-repeat 7px 2px',
			'border-bottom': '1px solid #716f64',
			'border-top': '1px solid #e0dfd0',
			'padding': 0,
			'margin': 0,
			'position': 'fixed',
			'width': '100%',
			'height': '21px',
			'left' : 0,
			'top' : 0,
			// css hacks for old IE
			'_position' : 'absolute', 
			'_top': 'expression(eval(document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop))',
			'_left' : 'expression(eval(document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft))',
			'_width' : 'expression(eval(document.documentElement.clientWidth ? document.documentElement.clientWidth : document.body.clientWidth))'
		},
		span: {
			'display': 'block',
			'float': 'right',
			'padding': '2px 7px 2px 7px',
			'margin': 0,
			'cursor': 'pointer',
			'font': '12px Verdana',
			'color': '#536482'		
		},
		infotext: {
			'display': 'block',
			'text-decoration': 'none',
			'cursor': 'pointer',
			'padding': '3px 0 2px 26px',
		    'margin': '0 30px 0 0',
		    'font': '11px Verdana',
		    'color': '#536482'
		},
		shadow: {
			'height': '22px', 
		    'padding': 0, 
		    'margin': 0
		} 	
	},
	toCss = function(obj){
		var item, res = '', hasOwnProperty = Object.prototype.hasOwnProperty;
		for(item in obj){
			if (hasOwnProperty.call(obj, item)){
			    res +=  item + ": " + obj[item] + "; ";  
            }
		}
		res = res.slice(0, -2);
		return res;
	},
	messageText = 'Ваш браузер не поддерживается. Сайт будет работать неправильно. Чтобы исправить проблему, нажмите здесь.'
	;

	var html = '<div id="oldies-bar" ' + 'style="' + toCss(styles.bar) + '"' + '>' + 
	   '<span style="' + toCss(styles.span) + '"' + 
	  'onclick="document.getElementById(\'oldies-shadow\').style.display=\'none\'; document.getElementById(\'oldies-bar\').style.display=\'none\';">×</span>' + 
	  '<div id="oldies-infotext" style="' + toCss(styles.infotext) +  '"' +  
	  'onclick="typeof jQuery !== &quot;undefined&quot; && $(&quot;.form-help>span&quot;).click(); ";>' +
	  messageText + 
	  '</div></div><div id="oldies-shadow" ' + 
	  'style="' + toCss(styles.shadow) +  '"></div>';
	
	document.write(html);
	document.getElementById("oldies-infotext").appendChild(infoBox);
	document.getElementById("oldies-infotext").onclick = function() {
		openFlag = !openFlag;
		if (openFlag) {
			document.getElementById("oldies-bar").style.height = "285px";
		} else {
			document.getElementById("oldies-bar").style.height = "23px";
		}
	};
})();