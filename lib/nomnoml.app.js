var nomnoml = nomnoml || {}

$(function (){

function startDraw() {

	var storage = buildStorage(location.hash)
	var jqCanvas = $('#canvas')
	var viewport = $(window)
	var lineNumbers = $('#linenumbers')
	var lineMarker = $('#linemarker')
	var jqTextarea = $('#textarea')
	var textarea = document.getElementById('data')
	var imgLink = document.getElementById('savebutton')
	var canvasElement = document.getElementById('canvas')
	var defaultSource = ""
	var graphics = skanaar.Canvas(canvasElement, {})

	initImageDownloadLink(imgLink, canvasElement)
	sourceChanged()

	nomnoml.toggleSidebar = function (id){
		var sidebars = ['reference', 'about']
		_.chain(sidebars).without(id).each(function (key){
			document.getElementById(key).classList.remove('visible')
		})
		document.getElementById(id).classList.toggle('visible')
	}

	nomnoml.discardCurrentGraph = function (){
		if (confirm('Do you want to discard current diagram and load the default example?')){
			textarea.innerHTML = defaultSource
			sourceChanged()
		}
	}

	function buildStorage(locationHash){
		if (locationHash.substring(0,6) === '#view/')
			return {
				read: function (){ return decodeURIComponent(locationHash.substring(6)) },
				isReadonly: true
			}
		return {
			read: function (){ return localStorage['nomnoml.lastSource'] || defaultSource },
			save: function (source){
				localStorage['nomnoml.lastSource'] = source
			},
			isReadonly: false
		}
	}

	function initImageDownloadLink(link, canvasElement){
		link.addEventListener('click', downloadImage, false);
		function downloadImage(){

			var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");

      		if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
				var html="<p>Right-click on image below and Save-Picture-As</p>";
		        html+="<img src='"+canvas.toDataURL()+"' alt='from canvas'/>";
		        var tab=window.open();
		        tab.document.write(html);
            }  
            else {
				var url = canvasElement.toDataURL('image/png')
				link.href = url;
            }

		}
	}

	function getConfig(d){
		return {
			arrowSize: +d.arrowSize || 1,
			bendSize: +d.bendSize || 0.3,
			direction: {down: 'TB', right: 'LR'}[d.direction] || 'TB',
			gutter: +d.gutter || 5,
			edgeMargin: (+d.edgeMargin) || 0,
			edges: {hard: 'hard', rounded: 'rounded'}[d.edges] || 'rounded',
			fill: (d.fill || '#eee8d5;#fdf6e3;#eee8d5;#fdf6e3').split(';'),
			fillArrows: d.fillArrows === 'true',
			font: d.font || 'Calibri',
			fontSize: (+d.fontSize) || 12,
			leading: (+d.leading) || 1.25,
			lineWidth: (+d.lineWidth) || 3,
			padding: (+d.padding) || 8,
			spacing: (+d.spacing) || 40,
			stroke: d.stroke || '#33322E',
			zoom: +d.zoom || 1
		}
	}

	function fitCanvasSize(rect, scale, superSampling){
		var w = rect.width * scale
		var h = rect.height * scale
		jqCanvas.attr({width: superSampling*w, height: superSampling*h})
		jqCanvas.css({
			top: 400 * (1 - h/viewport.height()),
			left: 0 + (viewport.width() - w)/2,
			width: w,
			height: h
		})
	}

	function setFont(config, isBold, isItalic){
		var style = (isBold === 'bold' ? 'bold ' : '')
		if (isItalic) style = 'italic ' + style
		graphics.ctx.font = style+config.fontSize+'pt '+config.font+', Helvetica, sans-serif'
	}

	function htmlDecode(input){
	  var e = document.createElement('div');
	  e.innerHTML = input;
	  return e.childNodes[0].nodeValue;
	}

	function parseAndRender(superSampling){

		var ast = nomnoml.parse(htmlDecode(textarea.innerHTML))

		var config = getConfig(ast.directives)

	    var measurer = {
	    	setFont: setFont,
	        textWidth: function (s){ return graphics.ctx.measureText(s).width },
	        textHeight: function (s){ return config.leading * config.fontSize }
	    }
		var layout = nomnoml.layout(measurer, config, ast)
		fitCanvasSize(layout, config.zoom, superSampling)
		config.zoom *= superSampling
		nomnoml.render(graphics, config, layout, setFont)
	}

	function sourceChanged(){
		try {
			var superSampling = window.devicePixelRatio || 1;
			lineMarker.css('top', -30)
			lineNumbers.css({background:'#eee8d5', color:'#D4CEBD'})
			parseAndRender(superSampling)
			storage.save(textarea.innerHTML)
		} catch (e){
			var matches = e.message.match('line ([0-9]*)')
			if (matches){
				var lineHeight = parseInt(jqTextarea.css('line-height'), 10)
				lineMarker.css('top', 8 + lineHeight*matches[1])
			}
			else {
				lineNumbers.css({background:'rgba(220,50,47,0.4)', color:'#657b83'})
				throw e
			}
		}
	}

}


$('#draw').click(function() {
    startDraw();

    $("#schema").show();
    $("#draw").hide();
});

})
