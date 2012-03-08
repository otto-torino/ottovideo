/*
 * layerWindow class
 *
 * layerWindow method: constructor
 *   Syntax
 *      var myLayerWindowInstance = new layerWindow([options]);
 *   Arguments 
 *	1. options - (object, optional) The options object.
 *   Options
 *	- id (string: default to null) The id attribute of the window container
 *	- bodyId (string: default to null) The id attribute of the body container
 *   	- title (string: default to null) The window title
 * 	- width (int: default to 400) The width in px of the window body
 * 	- height (int: default to null) The height in px of the body. By default its value depends on contained text
 *  	- minWidth (int: default to 300) The minimum width when resizing
 *	- minHeight (int: default to 100) The minimum height when resizing
 * 	- maxHeight (int: default to null) The max-height css property of the window body
 *	- draggable (bool: default to true) Whether or not to make the window draggable
 *	- resize (bool: default to true) Whether or not to make the window resizable
 *	- closeButtonUrl (string: default to null) The url of the image to use as close button
 *	- closeButtonLabel (string: default to close) The string to use as close button if the closeButtonUrl is null
 *	- destroyOnClose (bool: default to true) Whether or not to destroy all object properties when closing the window
 *  	- url (string: default to null) The url to be called by ajax request to get initial window body content
 *	- html (string: default to null) The initial html content of the window body if url is null
 *	- closeCallback (function: default to null) The function to be called when the window is closed
 *	- closeCallbackParam (mixed: default to null) The paramether to pass to the callback function when the window is closed
 *
 * layerWindow method: setTitle
 *  sets the title of the window and updates it if the window is showed
 *   Syntax
 *	myLayerWindowInstance.setTitle(title);
 *   Arguments
 *	1. title - (string) The title of the window
 *
 * layerWindow method: setHtml
 *  sets the content of the window and updates it if the window is showed
 *   Syntax
 *	myLayerWindowInstance.setHtml(html);
 *   Arguments
 *	1. html - (string) The html content of the window body
 *
 * layerWindow method: setUrl
 *  sets the content of the window and updates it if the window is showed
 *   Syntax
 *	myLayerWindowInstance.setUrl(url);
 *   Arguments
 *	1. url - (string) The url called by ajax request to get window body content
 *
 * layerWindow method: display
 *  displays the window in the position pointed by the element passed, or by the given coordinates
 *   Syntax
 *	myLayerWindowInstance.display(el, [opt]);
 *   Arguments
 *	1. el - (element) The element respect to which is rendered the window (top left of the window coincide with top left of the element)
 *      2. opt - (object) The top and left coordinates of the top left edge of the window. If only one is given the other is taken from the el passed
 *
 * layerWindow method: setFocus
 *  set focus on the object window, giving it the greatest z-index in the document
 *   Syntax
 *	myLayerWindowInstance.setFocus();
 *
 * layerWindow method: closeWindow
 *  closes the window and destroyes the object properties if the option destroyOnClose is true
 *   Syntax
 *	myLayerWindowInstance.closeWindow();
 *   
 */
var layerWindow = new Class({

	Implements: [Options],
	options: {
		id: null,
		bodyId: null,
		title: null,
		width: 400,
		height: null,
		minWidth: 300,
		minHeight: 100,
		maxHeight: null,
		draggable: true,
		resize: true,
		closeButtonUrl: null,
		closeButtonLabel: 'close',
		destroyOnClose: true,
		url:'',
		html: ' ',
		htmlNode: null,
		closeCallback: null,
		closeCallbackParam: null
	},
    	initialize: function(options) {
	
		this.showing = false;	

		if($defined(options)) this.setOptions(options);
		this.checkOptions();

		if($chk(this.options.title)) this.title = this.options.title;
		if($chk(this.options.html)) this.html = this.options.html;
		if($chk(this.options.htmlNode)) this.htmlNode = $type(this.options.htmlNode)=='element' ? this.options.htmlNode : $(this.options.htmlNode);
		if($chk(this.options.url)) this.url = this.options.url;
	},
	checkOptions: function() {
		var rexp = /[0-9]+/;
		if(!rexp.test(this.options.width) || this.options.width<this.options.minWidth) this.options.width = 400;
	},
	setTitle: function(title) {
		this.title = title;	 
		if(this.showing) this.header.set('html', title);
	},
	setHtml: function(html) {
		this.html = html;	 
		if(this.showing) this.body.set('html', html);
	},
	setUrl: function(html) {
		this.url = url;	 
		if(this.showing) this.request();
	},
	display: function(element, opt) {
		this.showing = true;
		this.element = $type(element)=='element'? element:$(element);
		var elementCoord = this.element.getCoordinates();
		this.top = (opt && $chk(opt.top)) ? opt.top < 0 ? 0 : opt.top : elementCoord.top;
		this.left = (opt && $chk(opt.left)) ? opt.left < 0 ? 0 : opt.left : elementCoord.left;
		this.renderContainer();
		this.renderHeader();
		this.renderBody();
		this.renderFooter();
		this.container.setStyle('width', (this.body.getCoordinates().width)+'px');
		this.initBodyHeight = this.body.getStyle('height').toInt();
		this.initContainerDim = this.container.getCoordinates();

		if(this.options.draggable) this.makeDraggable();
		if(this.options.resize) this.makeResizable();

	},
	renderContainer: function() {
		this.container = new Element('div', {'id':this.options.id, 'class':'abiWin'});

		this.container.setStyles({
			'top': this.top+'px',
			'left':this.left+'px'
		})
		this.setFocus();
		this.container.addEvent('mousedown', this.setFocus.bind(this));
		this.container.inject(document.body);
	},
	renderHeader: function() {
		this.header = new Element('header', {'class':'abiHeader'});
		this.header.set('html', this.title);

		var closeEl;
		if($chk(this.options.closeButtonUrl) && $type(this.options.closeButtonUrl)=='string') {
			closeEl = new Element('img', {'src':this.options.closeButtonUrl, 'class':'close'});
		}
		else {
			closeEl = new Element('span', {'class':'close'});
			closeEl.set('html', this.options.closeButtonLabel);
		}

		closeEl.addEvent('click', this.closeWindow.bind(this));
		this.header.inject(this.container, 'top');
		closeEl.inject(this.header, 'before');
    				
	},
	renderBody: function() {
		this.body = new Element('div', {'id':this.options.bodyId, 'class':'body'});
		this.body.setStyles({
			'width': this.options.width,
			'height': this.options.height,
			'max-height': this.options.maxHeight
		})
		this.body.inject(this.container, 'bottom');
		
		$chk(this.url) ? this.request() : $chk(this.htmlNode) ? this.body.set('html', this.htmlNode.clone(true,true).get('html')) : this.body.set('html', this.html);
	},
	renderFooter: function() {
		this.footer = new Element('footer');
		this.footer.inject(this.container, 'bottom');
    				
	},
	renderResizeCtrl: function() {
		this.resCtrl = new Element('div').setStyles({'position':'absolute', 'right':'0', 'bottom':'0', 'width':'10px', 'height':'10px', 'cursor':'se-resize'});
		this.resCtrl.inject(this.footer, 'top');		
	},
	makeDraggable: function() {
		var docDim = document.getCoordinates();
		if(this.options.draggable) {
			var dragInstance = new Drag(this.container, {
				'handle':this.header, 
				'limit':{'x':[0, (docDim.width-this.container.getCoordinates().width)], 'y':[0, ]}
			});
			this.header.setStyle('cursor', 'move');
		}
    
	},
	makeResizable: function() {
		this.renderResizeCtrl();
		var ylimit = $chk(this.options.maxHeight) 
			? this.options.maxHeight+this.header.getCoordinates().height+this.header.getStyle('margin-top').toInt()+this.header.getStyle('margin-bottom').toInt()+this.container.getStyle('padding-top').toInt()+this.container.getStyle('padding-bottom').toInt() 
			: document.body.getCoordinates().height-20;
		this.container.makeResizable({
			'handle':this.resCtrl, 
			'limit':{'x':[this.options.minWidth, (document.body.getCoordinates().width-20)], 'y':[this.options.minHeight, ylimit]},
			'onDrag': function(container) {this.resizeBody()}.bind(this),
			'onComplete': function(container) {this.makeDraggable()}.bind(this)
		});
	},
	resizeBody: function() {
		this.body.setStyles({
			'width': this.options.width.toInt()+(this.container.getCoordinates().width-this.initContainerDim.width),
			'height': this.initBodyHeight+(this.container.getCoordinates().height-this.initContainerDim.height)		
		});	      
	},
	request: function() {
		ajaxRequest('post', this.url, '', this.body, {'script':true, 'load':this.body});	 
	},
	setFocus: function() {
		if(!this.container.style.zIndex || (this.container.getStyle('z-index').toInt() < window.maxZindex))
			this.container.setStyle('z-index', ++window.maxZindex);
	},
	closeWindow: function() {
		this.showing = false;
		this.container.dispose();
    		if($chk(this.options.closeCallback)) this.options.closeCallback(this.options.closeCallbackParam);		
		if(this.options.destroyOnClose) for(var prop in this) this[prop] = null;
	}

})

function getViewport() {

	var width, height, left, top, cX, cY;

 	// the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight
 	if (typeof window.innerWidth != 'undefined') {
   		width = window.innerWidth,
   		height = window.innerHeight
 	}

	// IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
 	else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth !='undefined' && document.documentElement.clientWidth != 0) {
    		width = document.documentElement.clientWidth,
    		height = document.documentElement.clientHeight
 	}

	top = $chk(self.pageYOffset) 
		? self.pageYOffset 
		: (document.documentElement && $chk(document.documentElement.scrollTop))
			? document.documentElement.scrollTop
			: document.body.clientHeight;

	left = $chk(self.pageXOffset) 
		? self.pageXOffset 
		: (document.documentElement && $chk(document.documentElement.scrollTop))
			? document.documentElement.scrollLeft
			: document.body.clientWidth;

	cX = left + width/2;

	cY = top + height/2;

	return {'width':width, 'height':height, 'left':left, 'top':top, 'cX':cX, 'cY':cY};

}

window.maxZindex = getMaxZindex();

function getMaxZindex() {
	
	var maxZ = 0;
	$$('body *').each(function(el) {if(el.getStyle('z-index').toInt()) maxZ = Math.max(maxZ, el.getStyle('z-index').toInt())});

	return maxZ;

}

/*
 * hScrollingList class
 *
 * hScrollingList method: constructor
 *   Syntax
 *      var myInstance = new hScrollingList(list, vpItems, scrollableWidth, itemWidth, [options]);
 *   Arguments 
 *      1. list - (string|Object) The UL element or its id attribute to be transformed
 *      2. vpItems - (int) The number of element showed in a viewport (the viewport changes (scrolls) when clicking on the arrows)
 *      3. scrollableWidth - (int) The width in px of the scrollable object
 *      4. itemWidth - (int) The width in px of a list element
 *	5. options - (object, optional) The options object.
 *   Options
 *	- id (string: default to null) The id of the object
 *	- list_height (string: default to null) The height of the list, if null the height depends on the contents
 *	- selected (string: default to null) The intial selected item
 *	- selected_callback (function: default to null) The callback function to call after setting the selected item
 *	- selected_param (function: default to null) The parameter to pass to the callback function called after setting the selected item 
 *	- tr_duration (int: default to 1000) The duration in ms of the transaction betwen viewports
 *      ........ maybe many more options in the future.........
 *
 * hScrollingList method: updateCtrl
 * updates the status of the controllers and their actions
 *   Syntax
 *      myInstance.updateCtrl();
 *
 * hScrollingList method: deactivateCtrl
 * Disables the controllers (status OFF and no actions)
 *   Syntax
 *      myInstance.deactivateCtrl();
 *
 * vScrollingList method: setSelected
 * Selects a list item
 *   Syntax
 *      myInstance.setSelected(index, [callback], [param]);
 *   Arguments
 *     index (int default null): the index of the element to select, from 1 to list.length
 *     callback (function default null): the callback function to call after selection
 *     param (mixed default null): a parameter to pass to the callback function
 *
 */
var hScrollingList = new Class({

	Implements: [Options],
	options: {
		id: null,
		list_height: null,
		selected: null,
		selected_callback: null,
		selected_param: null,
                tr_duration: 1000 
		// maybe more options in the future here
	},
    	initialize: function(list, vpItems, scrollableWidth, itemWidth, options) {
	
		if($defined(options)) this.setOptions(options);

		this.list = $type(list)=='element'? list:$(list);
		this.list.setStyle('visibility', 'hidden'); // hide list transformations (vertical to horizontal)
		this.listElements = this.list.getChildren('li');

		this.setWidths(scrollableWidth, itemWidth);
		this.vpItems = vpItems;
		
		this.setSlide();
		this.setStyles();  // vpItems property may change!
		this.setWrapper();

		this.list.setStyle('visibility', 'visible'); // when the structure is ready, the list is showed

		this.tr = new Fx.Tween(this.slide, {
				'duration': this.options.tr_duration,
				'transition': 'quad:out',
				'onComplete' : function() {this.busy=false}.bind(this)
			});

		this.vps = 1;
		this.setSelected(this.options.selected, this.options.selected_callback, this.options.selected_param);

		this.tots = Math.ceil(this.listElements.length/this.vpItems);
		this.updateCtrl();


	},
	setWidths: function(tw, iw) {
		this.width = tw;
		this.ctrlWidth = 24;
		this.cWidth = this.width - 2*this.ctrlWidth;
		this.iWidth = iw;
	},
	setSlide: function() {
		var clear = new Element('div', {'styles':{'clear':'both'}});
		this.slide = new Element('div', {
			'styles': {'position':'relative', 'width':'10000em'},
			'class': 'slide'	
		});
		this.slide.inject(this.list, 'before');
		this.slide.grab(this.list);
		clear.inject(this.slide, 'bottom');
	},
	setWrapper: function() {
		this.wrapper = new Element('div', {
				'styles':{'width': this.width+'px'}
		});	    
		var ctrlHeight = this.listElements[0].getCoordinates().height;
		for(var i=1; i<this.listElements.length; i++) 
			if(this.listElements[i].getCoordinates().height > ctrlHeight) 
				ctrlHeight = this.listElements[i].getCoordinates().height;

		this.leftCtrl = new Element('div', {
			'styles': {'float': 'left', 'width': this.ctrlWidth+'px', 'height':ctrlHeight+'px'}
		})
		this.rightCtrl = new Element('div', {
			'styles': {'float': 'right', 'width': this.ctrlWidth+'px', 'height':ctrlHeight+'px'}		
		})
		this.itemContainer = new Element('div', {
			'styles': {'position': 'relative', 'overflow': 'hidden', 'float': 'left', 'width': this.cWidth+'px'}		
		})
		this.wrapper.adopt(this.leftCtrl, this.itemContainer, this.rightCtrl);
		this.wrapper.inject(this.slide, 'before');
		this.itemContainer.adopt(this.slide);
	},
	setStyles: function () {
		this.list.setStyles({'margin': '0', 'padding': '0', 'list-style-type':'none', 'list-style-position':'outside'});

		var esw = this.vpItems*this.iWidth;
		while(esw>this.cWidth) esw = --this.vpItems*this.iWidth;
		var margin = (this.cWidth - esw)/2;

		for(var i=0; i<this.listElements.length; i++) {
			var item = this.listElements[i];
			if(this.options.selected!='' && (this.options.selected-1)==i) item.addClass('selected');
			var r = i%this.vpItems;
			item.setStyles({
				'float':'left',
				'width': this.iWidth+'px',
				'margin-left': !i ? margin+'px' : r ? '0px' : 2*margin+'px',	
				'height': this.options.list_height ? this.options.list_height+'px' : 'auto'
			})
		}
	},
	scroll: function(d) {
		
		if(this.busy) return false;

		this.busy = true;
		if(d=='right') 
			this.tr.start('left', '-'+(this.cWidth*this.vps++)+'px');
		else if(d=='left') 
			this.tr.start('left', '-'+(this.cWidth*(--this.vps-1))+'px');
	
		this.updateCtrl();
	},
	updateCtrl: function() {

		var lclass = this.vps == 1 ? 'leftCtrlOff':'leftCtrl';
		var rclass = this.vps == this.tots ? 'rightCtrlOff':'rightCtrl';
		this.leftCtrl.setProperty('class', lclass);		    
		this.rightCtrl.setProperty('class', rclass);	    

		if(this.vps==1) {
			this.leftCtrl.removeEvents('mouseover');
			this.leftCtrl.removeEvents('mouseout');
			this.leftCtrl.removeEvents('click');
			this.le = false;
		}
		else if(!this.le) {
			this.leftCtrl.addEvent('mouseover', function() {this.setProperty('class', 'leftCtrlOver')});
			this.leftCtrl.addEvent('mouseout', function() {this.setProperty('class', 'leftCtrl')});
			this.leftCtrl.addEvent('click', this.scroll.bind(this, 'left'));
			this.le = true;
		}

		if(this.vps == this.tots) {
			this.rightCtrl.removeEvents('mouseover');
			this.rightCtrl.removeEvents('mouseout');
			this.rightCtrl.removeEvents('click');
			this.re = false;
		
		}
		else if(!this.re) {
			this.rightCtrl.addEvent('mouseover', function() {this.setProperty('class', 'rightCtrlOver')});
			this.rightCtrl.addEvent('mouseout', function() {this.setProperty('class', 'rightCtrl')});
			this.rightCtrl.addEvent('click', this.scroll.bind(this, 'right'));
			this.re = true;
		}

	},
	deactivateCtrl: function() {
		
		this.leftCtrl.removeEvents('mouseover');
		this.leftCtrl.removeEvents('mouseout');
		this.leftCtrl.removeEvents('click');
		this.le = false;	
		this.rightCtrl.removeEvents('mouseover');
		this.rightCtrl.removeEvents('mouseout');
		this.rightCtrl.removeEvents('click');
		this.re = false;
		this.leftCtrl.setProperty('class', 'leftCtrlOff');		    
		this.rightCtrl.setProperty('class', 'rightCtrlOff');
	},
	setSelected: function(index, callback, param) {
		// index from 1 to n
		if(!index) return null;
		this.vps = index ? Math.ceil(index/this.vpItems) : 1;
		this.tr.set('top', '-'+(this.cHeight*(this.vps-1))+'px');

		for(var i=0; i<this.listElements.length; i++) {
			if((index-1)==i) this.listElements[i].addClass('selected');
	   		else if(this.listElements[i].hasClass('selected')) this.listElements[i].removeClass('selected');		
		}

		if(callback) callback(param);
	}
});

/*
 * vScrollingList class
 *
 * vScrollingList method: constructor
 *   Syntax
 *      var myInstance = new vScrollingList(list, vpItems, scrollableWidth, itemWidth, [options]);
 *   Arguments 
 *      1. list - (string|Object) The UL element or its id attribute to be transformed
 *      2. vpItems - (int) The number of element showed in a viewport (the viewport changes (scrolls) when clicking on the arrows)
 *      3. scrollableHeight - (int) The height in px of the scrollable object
 *      4. itemHeight - (int) The height in px of a list element
 *	5. options - (object, optional) The options object.
 *   Options
 *	- id (string: default to null) The id of the object
 *	- list_width (string: default to null) The width of the list, if null the width takes all available space
 *	- selected (string: default to null) The intial selected item
 *	- selected_callback (function: default to null) The callback function to call after setting the selected item
 *	- selected_param (function: default to null) The parameter to pass to the callback function called after setting the selected item
 *	- tr_duration (int: default to 1000) The duration in ms of the transaction betwen viewports
 *      ........ maybe many more options in the future.........
 *
 * vScrollingList method: updateCtrl
 * updates the status of the controllers and their actions
 *   Syntax
 *      myInstance.updateCtrl();
 *
 * vScrollingList method: deactivateCtrl
 * Disables the controllers (status OFF and no actions)
 *   Syntax
 *      myInstance.deactivateCtrl();
 *
 * vScrollingList method: setSelected
 * Selects a list item
 *   Syntax
 *      myInstance.setSelected(index, [callback], [param]);
 *   Arguments
 *     index (int default null): the index of the element to select, from 1 to list.length
 *     callback (function default null): the callback function to call after selection
 *     param (mixed default null): a parameter to pass to the callback function
 *
 */
var vScrollingList = new Class({

	Implements: [Options],
	options: {
		id: null,
		list_width: null,
		selected: null,
		selected_callback: null,
		selected_param: null,
                tr_duration: 1000 
		// maybe more options in the future here
	},
    	initialize: function(list, vpItems, scrollableHeight, itemHeight, options) {
	
		if($defined(options)) this.setOptions(options);

		this.list = $type(list)=='element'? list:$(list);
		this.listElements = this.list.getChildren('li');

		this.setHeights(scrollableHeight, itemHeight);
		this.vpItems = vpItems;
		
		this.setSlide();
		this.setStyles();  // vpItems property may change!
		this.setWrapper();

		this.list.setStyle('visibility', 'visible'); // when the structure is ready, the list is showed

		this.tr = new Fx.Tween(this.slide, {
				'duration': this.options.tr_duration,
				'transition': 'quad:out',
				'onComplete' : function() {this.busy=false}.bind(this)
			});

		this.vps = 1;
		this.setSelected(this.options.selected, this.options.selected_callback, this.options.selected_param);

		this.tots = Math.ceil(this.listElements.length/this.vpItems);
		this.updateCtrl();

	},
	setHeights: function(th, ih) {
		this.height = th;
		this.ctrlHeight = 26;
		this.cHeight = this.height - 2*this.ctrlHeight;
		this.iHeight = ih;
	},
	setSlide: function() {
		var clear = new Element('div', {'styles':{'clear':'both'}});
		this.slide = new Element('div', {
			'styles': {'position':'relative', 'height':'10000em', 'padding-top':'2px'},  //margin collapsing	
			'class': 'slide'
		});
		this.slide.inject(this.list, 'before');
		this.slide.grab(this.list);
		clear.inject(this.slide, 'bottom');
	},
	setWrapper: function() {
		this.wrapper = new Element('div', {
			'styles':{'height': this.height+'px'}
		});	    

		this.topCtrl = new Element('div', {
			'styles': {'height': this.ctrlHeight+'px'}
		})
		this.bottomCtrl = new Element('div', {
			'styles': {'height':this.ctrlHeight+'px'}		
		})
		this.itemContainer = new Element('div', {
			'styles': {'position': 'relative', 'overflow': 'hidden', 'height': this.cHeight+'px'}		
		})
		this.wrapper.adopt(this.topCtrl, this.itemContainer, this.bottomCtrl);
		this.wrapper.inject(this.slide, 'before');
		this.itemContainer.adopt(this.slide);
	},
	setStyles: function () {
		this.list.setStyles({'margin': '0', 'padding': '0', 'list-style-type':'none', 'list-style-position':'outside'});

		var realHeight = this.cHeight - 4; // padding of slide element 2px, X2 for symmetry
		var esh = this.vpItems*(this.iHeight+1)+1; // border of li elements
		while(esh>realHeight) esh = --this.vpItems*(this.iHeight+1)+1; 
		var margin = (this.cHeight - esh)/2; // margin is calculated not considering the padding, which is considered in the margin of the first element only

		for(var i=0; i<this.listElements.length; i++) {
			var item = this.listElements[i];
			if(this.options.selected!='' && (this.options.selected-1)==i) item.addClass('selected');
			var r = i%this.vpItems;
			item.setStyles({
				'border-top': (r ? 0:1)+'px solid #000',
				'border-bottom': '1px solid #000',
				'height': this.iHeight+'px',
				'padding': '0',
				'float': 'left',
				'clear': 'left',
				'margin': '0',
				'margin-top': !i ? (margin-2)+'px' : r ? '0px' : 2*margin+'px',	
				'width': this.options.list_width ? this.options.list_width+'px' : '100%'
			});
		}
	},
	scroll: function(d) {
		
		if(this.busy) return false;

		this.busy = true;
		if(d=='bottom') 
			this.tr.start('top', '-'+(this.cHeight*this.vps++)+'px');
		else if(d=='top') 
			this.tr.start('top', '-'+(this.cHeight*(--this.vps-1))+'px');
	
		this.updateCtrl();
	},
	updateCtrl: function() {

		var tclass = this.vps == 1 ? 'topCtrlOff':'topCtrl';
		var bclass = this.vps == this.tots ? 'bottomCtrlOff':'bottomCtrl';
		this.topCtrl.setProperty('class', tclass);		    
		this.bottomCtrl.setProperty('class', bclass);	    

		if(this.vps==1) {
			this.topCtrl.removeEvents('mouseover');
			this.topCtrl.removeEvents('mouseout');
			this.topCtrl.removeEvents('click');
			this.te = false;
		}
		else if(!this.te) {
			this.topCtrl.addEvent('mouseover', function() {this.setProperty('class', 'topCtrlOver')});
			this.topCtrl.addEvent('mouseout', function() {this.setProperty('class', 'topCtrl')});
			this.topCtrl.addEvent('click', this.scroll.bind(this, 'top'));
			this.te = true;
		}

		if(this.vps == this.tots) {
			this.bottomCtrl.removeEvents('mouseover');
			this.bottomCtrl.removeEvents('mouseout');
			this.bottomCtrl.removeEvents('click');
			this.be = false;
		
		}
		else if(!this.be) {
			this.bottomCtrl.addEvent('mouseover', function() {this.setProperty('class', 'bottomCtrlOver')});
			this.bottomCtrl.addEvent('mouseout', function() {this.setProperty('class', 'bottomCtrl')});
			this.bottomCtrl.addEvent('click', this.scroll.bind(this, 'bottom'));
			this.be = true;
		}

	},
	deactivateCtrl: function() {
		
		this.topCtrl.removeEvents('mouseover');
		this.topCtrl.removeEvents('mouseout');
		this.topCtrl.removeEvents('click');
		this.te = false;	
		this.bottomCtrl.removeEvents('mouseover');
		this.bottomCtrl.removeEvents('mouseout');
		this.bottomCtrl.removeEvents('click');
		this.be = false;
		this.topCtrl.setProperty('class', 'topCtrlOff');		    
		this.bottomCtrl.setProperty('class', 'bottomCtrlOff');
	},
	setSelected: function(index, callback, param) {
		// index from 1 to n
		if(!index) return null;
		this.vps = index ? Math.ceil(index/this.vpItems) : 1;
		this.tr.set('top', '-'+(this.cHeight*(this.vps-1))+'px');

		for(var i=0; i<this.listElements.length; i++) {
			if((index-1)==i) this.listElements[i].addClass('selected');
	   		else if(this.listElements[i].hasClass('selected')) this.listElements[i].removeClass('selected');		
		}

		if(callback) callback(param);
	}
});

function copyToClipboard (text) {
	window.prompt ("Copia negli appunti: Ctrl+C, Enter", text);
}
