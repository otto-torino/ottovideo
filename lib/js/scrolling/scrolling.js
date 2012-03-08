var ScrollingStructure =  new Class({
						
						Implements: [Events, Options],		
								
						options: {
							duration:1400,
							startEvent: 'mouseover',
							stopEvent: 'mouseout'
						},
			
						initialize: function(mycontainer, myleftscroll, myrightscroll, options) {
							this.setOptions(options);
							this.container = mycontainer;
							this.leftscroll = myleftscroll;
							this.rightscroll = myrightscroll;
							this.cont_width = $(this.container).getStyle('width').toInt();
							this.item_width = $(this.container).getChildren()[0].getChildren()[0].getStyle('width').toInt();
										
							this.scroll = new Fx.Scroll(this.container, {
								wait: false,
								duration: this.options.duration,
								offset: {'x':(this.item_width-this.cont_width)/2, 'y': 0},
								transition: Fx.Transitions.Quad.easeOut
							});	
							this.scrollclick = new Fx.Scroll(this.container, {
								wait: false,
								duration: 300,
								offset: {'x':(this.item_width-this.cont_width)/2, 'y': 0},
								transition: Fx.Transitions.Quad.easeOut
							});	
						},
						
						start: function(clickfunc) {									
							$(this.leftscroll).addEvent(this.options.startEvent,function(event){
								event.stop();
								this.scroll.toLeft();
							}.bind(this));
							$(this.leftscroll).addEvent(this.options.stopEvent,function(event){
								event.stop();
								this.scroll.cancel();
							}.bind(this))
									
							$(this.rightscroll).addEvent(this.options.startEvent,function(event){
								event.stop();
								this.scroll.toRight();
							}.bind(this));
							$(this.rightscroll).addEvent(this.options.stopEvent,function(event){
								event.stop();
								this.scroll.cancel();
							}.bind(this));	
							
							var scrollclick = this.scrollclick;		
							var thisobj = this;					
							$(this.container).getChildren()[0].getChildren('div').addEvent('click', function(event) {
								event.stop();
								scrollclick.toElement(this).chain(function(){
									clickfunc($(this).getProperty('id'), thisobj);
								}.bind(this));
							})
											
						},
						
						stop: function() {
							$(this.container).getChildren()[0].getChildren('div').removeEvents('click')
							$$('#'+this.leftscroll,'#'+this.rightscroll).removeEvents(this.options.startEvent);
							$$('#'+this.leftscroll,'#'+this.rightscroll).removeEvents(this.options.stopEvent);
						}
					
					});
