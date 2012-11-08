  
<!-- YUI -->

<script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/yui/utilities/utilities.js"></script>

<!-- Excanvas -->
<!--[if IE]><script type="text/javascript" src="../lib/excanvas.js"></script><![endif]-->

<!-- WireIt -->

<script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/WireIt.js"></script>
<script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/CanvasElement.js"></script>
<script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/Wire.js"></script>
<script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/Terminal.js"></script>
<script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/util/DD.js"></script>
<script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/util/DDResize.js"></script>
<script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/Container.js"></script>
<script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/Layer.js"></script>

<link rel="stylesheet" type="text/css" href="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/css/WireIt.css" />

<style>

#container {
	height: 350px;
	position: relative;
        background-color: #FFF5DF;
}

</style>
<script>

WireIt.samples = [
	{
		innerHTML: '<p>This presentation will show you some features of WireIt through interactive examples.</p>'+
							 '<h2>Try to create a wire by "drag-dropping" the "terminals".</h2>'+
							 '<div style="width: 100px;"></div>',
		init: function() {
			var t1 = new WireIt.Terminal(SampleMgr.container, {direction: [1,0], offsetPosition:[80,100] });
			var t2 = new WireIt.Terminal(SampleMgr.container, {direction: [-1,0], offsetPosition:[200,210] });
			this.t1 = t1;
			this.t2 = t2;
				
			t1.eventAddWire.subscribe(function(e, args) {
				var wire = args[0];
				if(wire.terminal1 == t2 || wire.terminal2 == t2) {
					SampleMgr.container.appendChild(WireIt.cn('h2', null, null, "Good ! Now try to cut this wire. (click on the scissors)") );
				}
			});
			
			t1.eventRemoveWire.subscribe(function(e, args) {
				var wire = args[0];
				if(wire.terminal1 == t2 || wire.terminal2 == t2) {
					var wire = args[0];
					SampleMgr.container.appendChild(WireIt.cn('h2', null, null, "Perfect ! <button onclick='SampleMgr.next()'>Next</button>") );
				}
			});
		},
		unload: function() {
			this.t1.remove();
			this.t2.remove();
		}
	},
	
	{
		innerHTML: '<p>Let\'s draw some arrows</p>',
		init: function() {
			var wireConfig = { drawingMethod: "arrows"};
			this.terminals = [
				new WireIt.Terminal(SampleMgr.container, { wireConfig: wireConfig, offsetPosition:[80,100] }),
				new WireIt.Terminal(SampleMgr.container, { wireConfig: wireConfig, offsetPosition:[80,210] }),
				new WireIt.Terminal(SampleMgr.container, { wireConfig: wireConfig, offsetPosition:[200,210] }),
				new WireIt.Terminal(SampleMgr.container, { wireConfig: wireConfig, offsetPosition:[200,100] })
			];
		},
		unload: function() {
			for(var i = 0 ; i < this.terminals.length ; i++) {
				this.terminals[i].remove();
			}
		}
	},
	
	{
		innerHTML: '<p>Straight lines too</p>',
		init: function() {
			this.terminals = [
				new WireIt.Terminal(SampleMgr.container, { wireConfig: { drawingMethod: "straight"}, offsetPosition:[80,100] }),
				new WireIt.Terminal(SampleMgr.container, { wireConfig: { drawingMethod: "straight"}, offsetPosition:[80,210] }),
				new WireIt.Terminal(SampleMgr.container, { wireConfig: { drawingMethod: "straight"}, offsetPosition:[200,210] }),
				new WireIt.Terminal(SampleMgr.container, { wireConfig: { drawingMethod: "straight"}, offsetPosition:[200,100] })
			];
		},
		unload: function() {
			for(var i = 0 ; i < this.terminals.length ; i++) {
				this.terminals[i].remove();
			}
		}
	},
	
	{
		innerHTML: '<p>Typed terminals</p>',
		init: function() {
			this.terminals = [];
			var colors = ["red", "green", "blue"];
			for(var x = 0 ; x < 5 ; x++) {
				for(var y = 0 ; y < 4 ; y++) {
					var color = colors[(x+y*5) % colors.length];
					var t = new WireIt.Terminal(SampleMgr.container, {
						fakeDirection:  [0,1],
						offsetPosition:[50+x*60,50+y*60],
						ddConfig: {
					      type: color
						}
					});
						YAHOO.util.Dom.setStyle(t.el, "background-color", color);
						YAHOO.util.Dom.setStyle(t.el, "opacity", "0.5");
					this.terminals.push(t);
				}
			}
		},
		unload: function() {
			for(var i = 0 ; i < this.terminals.length ; i++) {
				this.terminals[i].remove();
			}
		}
	},
	
	{
		innerHTML: '<p>Thank you for viewing this presentation</p>'+
							 '<a href="..">Back to WireIt</a>',
		init: function() {}
	}
];

var SampleMgr = {
	sampleIndex: 0,
	init: function() {
		this.container = YAHOO.util.Dom.get('container');
		this.next();
	},
	previous: function() {
		this.sampleIndex = Math.max(this.sampleIndex-2,0);
		this.next();
	},
	next: function() {
		if(!YAHOO.lang.isUndefined(this.lastLoaded) && YAHOO.lang.isFunction(WireIt.samples[this.lastLoaded].unload) ) {
			 WireIt.samples[this.lastLoaded].unload(); 
		}
		if(this.sampleIndex == WireIt.samples.length) return;
		this.container.innerHTML = WireIt.samples[this.sampleIndex].innerHTML;
		this.lastLoaded = this.sampleIndex;
		WireIt.samples[this.sampleIndex].init();
		this.sampleIndex += 1;
	}
}


$(function() {
    SampleMgr.init();
});
/*
window.onload = function() {
    alert("teste");
	SampleMgr.init();
};
*/
</script>

 
<h1>WireIt interactive presentation</h1>
        
<div id="container">
</div>
        
<button onclick="SampleMgr.previous();" href="">Previous</button> <button onclick="SampleMgr.next();" href="">Next</button>