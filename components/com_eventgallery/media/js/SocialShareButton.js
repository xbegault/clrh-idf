window.addEvent("domready", function() {


	$(document.body).addEvent('click:relay(a.social-share-button-close)', function(e) {
		e.preventDefault();
		var myDiv = $(e.target).getParent('.social-sharing-toolbox');
		var myFx = new Fx.Tween(myDiv, {
		    property: 'opacity',
		    onComplete : function() {myDiv.dispose();}
		});
		myFx.start(0);		
	});	
		
	
			
	$(document.body).addEvent('click:relay(a.social-share-button-open)', function(e) {

		e.preventDefault();

		var link = $(e.target);
		if (!link.getAttribute('data-link')) {
			link = link.getParent('a');	
		}
		var id = 'id_' + Math.ceil(Math.random()*10000000);
		
		var targetPos = $(e.target).getPosition();
		var myDiv = new Element('div', {
		    href: '#',
		    'class': 'social-sharing-toolbox',
		    html: 'Loading',
		    id: id,
		    styles: {
		    	'opacity': '1 !important',
		    	'position': 'absolute',    	
		    	'top': targetPos.y-10,
		    	'left': targetPos.x-10,
		    	'opacity': 0
		    }
		});		
	
		
		$$('body')[0].grab(myDiv);
		
		myDiv.fade('in');
		myDiv.set('load', {evalScripts: true});			
		myDiv.load(link.getAttribute('data-link'));

		var timer = null;
					
		var closeFunction = function(){			
			var myFx = new Fx.Tween(myDiv, {
			    property: 'opacity',
			    onComplete : function() {myDiv.dispose();}
			});
			myFx.start(0);
			$$('body').removeEvent('click', closeFunction2);
		}		
		
		myDiv.addEvent('mouseleave', function(){
			timer = window.setTimeout(closeFunction, 1000);
		}.bind(this));
		
		myDiv.addEvent('mouseenter', function() {
			window.clearTimeout(timer);			
		}.bind(this));
		
		
		// this method is used to close the sharing windows if we click somewhere else.
		var closeFunction2 = function(e) {
			//console.log($(e.target).getParent('#' + id));
			if (e.target.id != id && $(e.target).getParent('#' + id) == null) {
				closeFunction();
			}
			//console.log('close2');
		}.bind(this);
		
		$$('body').addEvent('click', closeFunction2);			
		
	}); 
});