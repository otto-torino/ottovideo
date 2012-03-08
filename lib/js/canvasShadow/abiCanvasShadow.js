//AbiCanvasShadow, Mootools Canvas Dropshadow Abidibo extended
/*
Script: CanvasShadow.js
	Contains the Canvas class.

Dependencies:
	MooTools, <http://mootools.net/>
		Element, and its dependencies
		Element.Dimensions
	MooCanvas, <http://ibolmo.com/projects/moocanvas/>
		Canvas,
		Paths

Author:
	Arian Stolwijk, <http://www.aryweb.nl/>
	
Adjusted by 
	Abidibo, <http://abidibo.otto.to.it/>

License:
	MIT License, <http://en.wikipedia.org/wiki/MIT_License>
*/


var AbiCanvasShadow = new Class({
	
	/**
	 * This function creates a new canvas element of MooCanvas
	 * The elements will be placed at the right position, so the 
	 * canvas element is right behind the to-shadow element
	 * 
	 * @param {Object} shadowDiv this div will get a shadow
	 * @param {Object} options the options:
	 * 		size: The size of the shadow
	 * 		radius: the radius of the shadow corners
	 * 		opacity: The opacity of the shadow
	 * 		color: The shadow color, this should be like #FF9900, 
	 * 			or an array with the rgb colors [255,0,255]
	 * 		overwrite: if the canvas element already exists, 
	 * 			it wil dispose that element and create a new one
	 */	
	initialize: function(shadowDiv,options){		
		// Set shadow div
		this.shadowDiv = shadowDiv;
		// Get the coordinates of the element
		this.position = shadowDiv.getCoordinates();
		// Set some options
		this.size = options.size;
		var radius = options.radius;
		var opacity = options.opacity;
		
		if($(shadowDiv.get('id')) == false){
			this.giveId(shadowDiv);
		}
		
		// Dispose the already existing element, if needed
		if ($type(options.overwrite) == 'boolean' && options.overwrite == true) {
			if ($type($(shadowDiv.get('id') + '_mooShadow')) == 'element') {
				$(shadowDiv.get('id') + '_mooShadow').dispose();
			}
		}

		// Create a new Canvas object, of MooCanvas
		this.canvas = new Canvas({
			'width': this.position.width + (this.size * 2),
			'height': this.position.height + (this.size * 2),
			'id': shadowDiv.get('id')+'_mooShadow'
		});
		
		// Create a div and put the canvas element in it to set it at the right position
		this.createCanvasDiv();

		// Create the context
		var ctx = this.canvas.getContext("2d");
		
		// Create the shadow color
		if(options.color.test(/#[0-9A-Za-z]{6}|[0-9A-Za-z]{3}/)){
			options.color = options.color.hexToRgb(true);
		}
		if($type(options.color) == 'array' && $type(options.color[2]) != 'undefined'){
			var color = options.color[0]+','+options.color[1]+','+options.color[2];	
		}else{
			var color = '0,0,0';
		}

		// Create the retangles/ the actual shadow
		for (var i = this.size; i >= 0; i--) {
			this.roundedRect(ctx,
				i,
				i,
				(this.position.width-(i*2)+2*this.size),
				(this.position.height-(2*i)+2*this.size),
				radius
			);
			ctx.fillStyle = "rgba("+color+", "+opacity/(this.size-i)+")";
			ctx.fill();
		}

	},
	
	/**
	 * This function creates a div where the canvas element is in inserted
	 * This element will position behind the to-shadow div
	 */
	createCanvasDiv: function(){
		this.canvasDiv = new Element('div',{
			styles:{
				'width': this.position.width+(this.size*2),
				'height': this.position.height+(this.size*2),
				'z-index': -1,
				'position': 'absolute',
				'left': '-'+this.size+'px',
				'top':  '-'+this.size+'px'
			}
		}).adopt(this.canvas).inject(this.shadowDiv);
	},
	
	/**
	 * This method creates a rounded rectangle
	 * @param {Object} ctx the canvas context
	 * @param {Object} x the upper left x-axis position
	 * @param {Object} y the upper left y-axis positoin
	 * @param {Object} width the retangle width
	 * @param {Object} height the retangle height
	 * @param {Object} radius the corner radius
	 */
	roundedRect: function (ctx,x,y,width,height,radius){

		ctx.beginPath();
		ctx.moveTo(x,y+radius);
		ctx.lineTo(x,y+height-radius);
		ctx.quadraticCurveTo(x,y+height,x+radius,y+height);
		ctx.lineTo(x+width-radius,y+height);
		ctx.quadraticCurveTo(x+width,y+height,x+width,y+height-radius);
		ctx.lineTo(x+width,y+radius);
		ctx.quadraticCurveTo(x+width,y,x+width-radius,y);
		ctx.lineTo(x+radius,y);
		ctx.quadraticCurveTo(x,y,x,y+radius);
	},
	
	/**
	 * This method set an random ID to an element
	 * @param {Object} div
	 */
	giveId: function(div){
		div.set('id',$rand()+'_mooShadow');
	}
	
});

/**
 * 
 * @param {Object} options: the same options as the mooCanvasShadow() class
 */
Element.implement({
	AbiCanvasShadow: function (options){
		new AbiCanvasShadow(this,options);		
		return this;
	}
});
