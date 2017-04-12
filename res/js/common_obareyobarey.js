/*
	04.02.2015 00:55 -> jquery dependency %2 ( sortable )
*/



// kendi ready fonksiyonum
function AHReady( cb ){
	// Chrome, ff, opera.. > ie8
	if( document.addEventListener ){
		document.addEventListener( "DOMContentLoaded", cb, false );
	// <= ie8	
	} else if( document.attachEvent ){
		document.attachEvent("onreadystatechange", function(){
			if( document.readyState === "complete" ){
				cb();
			}
		});
	// eksantrik browserlar icin her turlu calisacak ready
	} else {
		var old_onload = window.onload;
		window.onload = function(){
			old_onload && old_onload();
			cb();
		}
	}
}
// class selector
function $AHC( cs ){
	var el_array = [];
	// ie 8 ve altinda tum dom u tara ve className uyan
	// elementleri listeye ekle
	if( !document.querySelectorAll || ( window.attachEvent && !window.addEventListener ) ){
		var tmp = document.getElementsByTagName("*"),
			regex = new RegExp("(^|\\s)" + cs + "(\\s|$)"),
			l = tmp.length;
		for( var i = 0; i < l; i++ ){
			if( regex.test(tmp[i].className) ) el_array.push(tmp[i]);
		}
		// cache de kalmasin
		tmp = [];
	} else {
		el_array = document.querySelectorAll( "." + cs );
	}
	// // tek seçim icin
	if( el_array.length == 1 ) return el_array[0];
	return el_array;
}

// assoc array
Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

function is_defined( vari ){
	return (typeof vari !== 'undefined');
}

function is_element( o ){
	return (
		typeof HTMLElement === "object" ? o instanceof HTMLElement :
		o && typeof o === "object" && o !== null && o.nodeType === 1 && typeof o.nodeName === "string"
	);
}

function get_object_type(obj, type){
	return Object.prototype.toString.call( obj ) === '[object '+type+']';
}
	
// id selector
function $AH(id){
	return document.getElementById(id);
}

// @parent elementinin child elementlerinde arama
function find_elem( parent, context ){
	var found = [], i,
		list = get_children( parent ), len = list.length;
	// tum elementler icin kontrol fonksiyonu calistir
	// uyanlari ekle
	for( i = 0; i < len; i++ ){
		if( match_context( list[i], context ).length > 0 ){
			found.push(list[i]);
			delete list[i];
		}
	}
	if( found.length == 1 ) return found[0];
	return found;
}

// @par altindaki tum children elementleri bul
function get_children( par ){
	var nodes = par.childNodes, len = nodes.length, elem_list = [], i;
	// birinci seviye childrenlar icin for baslat
	for( i = 0; i < len; i++ ){
		if( nodes[i].nodeName != "#text" && nodes[i].nodeName != "#comment" ){
			// ekle bulunanlara
			elem_list.push(nodes[i]);
			// simdi kontrol ettigimiz elementin alt elementlerine bak
			var children = get_children(nodes[i]);
			// varsa her birini bulunanlara ekle
			// recursive fonksiyon kullaniyorum
			// her element icin children var mi, varsa ekle yapiyoruz
			if( children ){
				foreach( children, function(node){
					elem_list.push(node);
				});		
			}
		}
	}
	// bosalt bunu
	nodes = [];
	return elem_list;
}

// @elem icin contextte verilen icerige uygunluk kontrolu yap
// .class, #id, [attr], li, input vs(node name kontrolu)
function match_context( elem, context ){
	var match = [];
		context = context.replace(/ /g,"");
	// class
	if( context.indexOf(".") > -1 ){
			if( hasClass( elem, context.substr(1) ) ) match.push( elem );
	// id
	} else if( context.indexOf("#") > -1 ){
		if( elem.id == context.substr(1) ) match.push( elem );
	// attr
	} else if( context.indexOf("[") > -1 ){
		// son ve ilk köşeli parantezleri temizle
		var attr_name = context.substr(1);
		attr_name = attr_name.substr( 0, attr_name.length - 1 );
		if( elem.getAttribute(attr_name) != null ) match.push( elem );
	// elem tip
	} else {
		if( elem.nodeName == context.toUpperCase() ) match.push( elem );
	}
	return match;
}


// elementin indexini bul
function get_node_index(node) {
	var index = 0;
	// bir elementin indexi o elementin oncesindeki element sayisindan bir fazla
	// ama baslangic sifir kabul ettigimiz icin direk eşit
	// onceki eleman null olana kadar yani ilk indexe gelene kadar degiskeni
	// arttiriyoruz ve indexi buluyoruz
	while ( (node = node.previousSibling) ) {
		// yalnizca DOM elementleri sayiyoruz
		if (node.nodeType != 3 || !/^\s*$/.test(node.data))	index++;
	}
	return index;
}


// ilk parenti bul	
function get_parent( elem ){
	if( elem && elem.parentNode ){
		return elem.parentNode;
	}
	return false;
}

// documente kadar parentlarini bul
function get_parents( elem ){
	var parents = [];
	// parent oldugu surece arraye ekle
	while( get_parent( elem ) ){
		parents.push( get_parent(elem) );
		elem = get_parent(elem);
	}
	return parents;
}


// direk elem yada ID si gelenin value yu döner
function get_val( elem ){
	if( is_element( elem ) ) {
		return elem.value;
	} else {
		return $AH(elem).value;
	}
	
}

function is_numeric( val ){
	return (val - 0) == val && trim( (''+val) ).length > 0;
}

// removeChild dan ziyade crossbrowser calisiyor
//https://developer.mozilla.org/en-US/docs/Web/API/Element/outerHTML
function remove_elem( elem ){
	if( elem ) elem.outerHTML = "";
}

function create_element( tag, class_names ){
	var elem = document.createElement( tag ),
	class_name = "";
	foreach( class_names, function( class_name ){
		addClass(elem, class_name );
	});
	return elem;
}

function create_img( src, alt ){
	var img =  document.createElement( "img" );
	img.src = src;
	img.alt = alt;
	return img;
}

function set_html( elem, cont ){
	if( elem ) elem.innerHTML = cont;
}

function get_html( elem ){
	if( elem ) return elem.innerHTML;
	return "";
}

function append_html( elem, content ){
	// console.log( content );
	if( elem ){
		var old_content = get_html( elem );
		set_html( elem, old_content + content );
	} 
}

function prepend_html( elem, content ){
	if( elem ){
		var old_content = get_html( elem );
		set_html( elem, content + old_content );
	}
}

// selectore gore elementlere event ekle
// birden fazla elementi foreachsiz burada handle edebiliyoruz
function add_event( selector, event, cb ){
	if( get_object_type(selector, "NodeList") || get_object_type(selector, "Array") ){
		foreach( selector, function(elem){
			add_event_to( elem, event, cb );
		});
	} else {
		add_event_to(selector, event, cb);
	}

}

// add event cross browser
// this keywordu kullanabiliyoruz
function add_event_to(elem, event, cb) {
	// addEventlistener destekleyenler icin
	function listen_handler(e) {
		// this icin
		var ret = cb.apply(this, arguments);
		if (ret === false) {
			e.stopPropagation();
			e.preventDefault();
		}
		return(ret);
	}
	// IE<9
	function attachHandler() {
		var ret = cb.call(elem, window.event);   
		if (ret === false) {
			window.event.returnValue = false;
			window.event.cancelBubble = true;
		}
		return(ret);
	}
	if( !elem ) return;
	// duruma gore eventleri bagla elemente
	if (elem.addEventListener) {
		elem.addEventListener(event, listen_handler, false);
	} else {
		elem.attachEvent("on" + event, attachHandler);
	}
}

// IE ve diger browserler icin preventDefault
function event_prevent_default( event ){
	( event.preventDefault ) ? event.preventDefault() : ( event.returnValue = false );
}
// IE ve diger browserler icin stopProp
function event_stop_propagation( event ){
	( event.stopPropagation ) ? event.stopPropagation() : ( window.event.cancelBubble = true );
}


function add_event_on( elem, find, event, cb ){
	var selector, off_target = false;
	// eger bulunacak elem  false ise direk document click, element aramiyoruz
	// off target falan mevzularinda kullanmak icin
	// diger turlu selectorun id veya class ismini al
	if( !find ) {
		off_target = true;
	} else {
		// attr bulma

		if( find.indexOf("[") > -1 ){
			selector = find.substr( 1, (find.length - 2)  );
		} else {
			selector = find.substr( 1 );
		}
		// console.log( selector );
	}
	add_event( elem, event, function(e){
		if( !off_target ) {
			// IE8 icin e.target srcElement onun icin kontrol
			var targ = e.target;
			if( !targ ) targ = window.event.srcElement;
			// Firefox ibnesi select option larina basildiginda, target olarak
			// opiton u aliyor o yuzden onun icin kontrol. eger optionsa parenti al (select)
			if( targ.nodeName == "OPTION" ) targ = targ.parentNode;
			// class veya id tutan eleman varsa callback calistir 
			// elem i de callback e argument olarak gec ( this )
			if( hasClass( targ, selector ) || targ.id == selector || targ.getAttribute(selector) != undefined ){
				cb( targ, e );
				return;
			}
			// console.log( get_parents(targ));
			// event bubble icin. en icteki elemente basildiginda parentlari da
			// kontrol et
			var parents = get_parents( targ ), len = parents.length;
			for( var i = 0; i < len; i++ ){
				if( hasClass( parents[i], selector ) || parents[i].id == selector ){
					cb( parents[i], e );
					break;
				}
			}
			
		} else {
			cb();
		}
	});
}

function sort_by_key(array, key, type ) {
	if( type == 'date' ){
		return array.sort(function(a, b) {
			var x = new Date(a), 
			y = new Date(b);
			if(x < y) return -1;
			if(x > y) return 1;
			return 0;
		});
	} else if( type == 'string' ){
		return array.sort(function(a, b) {
			var x = a[key], 
			y = b[key];
			return ((x < y) ? -1 : ((x > y) ? 1 : 0));
		});
	} else if( type == 'numeric' ){
		return array.sort(function(a,b) {
			var x = a[key], y = b[key];
		    return x - y;
		});
	}
}



// select e options ekle
// @elem -> select
// @clear -> true ise varolan option varsa temizler, false ise append
// @options -> option array
function add_options( elem, clear, options, selected ){
	if( clear ) set_html(elem, "");
	foreach( options, function(option){
		var opt = document.createElement('OPTION');
		opt.value = option[0];
		opt.text  = option[1];
		// console.log(option)
		if( selected && selected == option[0] ) opt.selected  =  true;
		elem.options.add( opt );
	});
}

function show(e){
	css( e, { display:"block"} );
	// e.style.display = "block";
}

function hide(e){
	css( e, { display:"none"} );
}


function toggle_class( elem, cls ){
	if(elem){
		if(hasClass(elem, cls)){
			removeClass(elem, cls);
		} else {
			addClass(elem,cls);
		}
	}
}

function hasClass(element, cls) {
	if( element ) return (' ' + element.className + ' ').indexOf(' ' + cls + ' ') > -1;
}

function addClass(element, cls){
	if( element ) if( !hasClass(element, cls ) ) element.className += ' ' + cls;
}

function removeClass(element, cls) {
	if( element ) {
		var newClass = ' ' + element.className.replace( /[\t\r\n]/g, ' ') + ' ';
		if( hasClass(element, cls ) ){
			while( newClass.indexOf(' ' + cls + ' ' ) >= 0 ){
				newClass = newClass.replace( ' ' + cls + ' ', ' ' );
			}
			element.className = newClass.replace( /^\s+|\s+$/g, '' );
		}
	}
}

// cross-browser trimjanim preg yalarim
function trim(str){
	return str.replace(/ /g,"");
}

// array in her bir elemani icin callback
function foreach( array, cb ){
	var i, l = array.length;
	for( var i = 0; i < l; i++ ){
		cb( array[i] );
	}
}

// object extend olayi
// x objesine, y deki objeleri ekle yada overwrite
function extend(x, y) {
	var i;
	if (!x) x = {};
	for (i in y) {
		x[i] = y[i];
	}
	return x;
};
// bunda örtüşmeyen elemanlar devredışı
// ayni metod ve degiskenler overwrite ediliyor
function overwrite(x, y){
	var i;
	if (!x) x = {};
	for (i in y) {
		if( x[i] != undefined ) x[i] = y[i];
	}
	return x;
}

function in_object( item, object ){
	for( var key in object ){
		if( object[item] != undefined ) return true;
	}
	return false;
}

function in_array( elem, array ){
	for( var i = 0; i < array.length; i++ ){
		if( elem == array[i] ) return true;
	}
	return false;
}
function remove_from_array( elem, array ){
	for( var i = 0; i < array.length; i++ ){
		if( elem == array[i] ) array.splice(i, 1);
	}
}

function css(elem, style) {
	// console.log(elem);
	extend(elem.style, style);
}

function get_return_url(){
	return (location.href).substr(29);
}

function debounce(func, frekans, ilkSefer) {
	// Her çağrılışta sıfırlanan bayrak ( istenen geckikmeyi algılayan )
	 var timeout;
	 return function debounced () {
		// debounce fonksiyonu ve args
		var obj = this, args = arguments;
		// Eğer ilk seferde debounce istemiyorsak direk fonksiyonu çalıştır
		// timeout'u sıfırla
		function delayed () {
			if (!ilkSefer) {
				func.apply(obj, args);
			}
			timeout = null; 
		}
		// Eğer delayden öncse basıldıysa timeout'u sıfırla
		if (timeout) {
			clearTimeout(timeout);
		}
		// Eğer delay şartı sağlanmışsa ve ilkSeferde delay istemiyorsak
		// Fonksiyonu çalıştırıyoruz.
		else if (ilkSefer) {
			func.apply(obj, args);
		}
		// Timeout' u resetledik
		timeout = setTimeout(delayed, frekans || 100); 
	};
}

var AHAJAX_V3 = {
	req: function( url, data, cb ){
		var xhr;
		// DT_functions.row_loader(true);
        // modern browserlar da XMLHttpRequest kullan
        if(typeof XMLHttpRequest !== 'undefined') {
        	xhr = new XMLHttpRequest();
        } else {
            // IE 6 dayinin ibneliklerini çözüyoruz
            var versions = ["MSXML2.XmlHttp.5.0",  "MSXML2.XmlHttp.4.0", "MSXML2.XmlHttp.3.0", "MSXML2.XmlHttp.2.0", "Microsoft.XmlHttp"]        
            // uyan versiyonu kullaniyoruz tek tek kontrol edip
            for(var i = 0, len = versions.length; i < len; i++) {
            	try {
            		xhr = new ActiveXObject(versions[i]);
            		break;
            	}
            	catch(e){}
            }
        }
        // ajax requesti yaptiginda onreadystatechange e tanimlanmis
        // fonksiyon 5 defa calisacak. her calismada durum ile ilgili bilgiye
        // gore fail, complete fonksiyonlari calistirabiliyoruz
        xhr.onreadystatechange = state_check;
        // kontrol fonksiyonu
        function state_check() {
            // uninitialized, loading, loaded, interactive state lerinde
            // birsey yapma requeste devam
            if(xhr.readyState < 4) {
            	return;
            };
            // network level de bir hata olursa calisiyor. ( cross domain vs )
            xhr.onerror = function(){ console.log( "Ajax request failed" ); };
            // readyState 4 request completed
            // status 200 HTTP OK
            if( xhr.readyState === 4 && xhr.status === 200 ) {
                // IE.7 ve altinda json.parse yok
           		// crockford dayinin kutuphanesini kullanabilirim 
           		var rsp = JSON.parse(xhr.responseText);
           		if( typeof cb == 'function' ) cb( rsp );
           		// DT_functions.row_loader(false);
                // console.log(xhr);
            } else if( xhr.status >= 500 || xhr.status <= 599 ){
            	// internal server error
            	console.log('INTERNAL SERVER ERROR - TEKRAR DENE');
            	cb( false );
            	// tekrar dene ( TEST )
            	//AHAJAX_V3.req( url, data, cb );
            }
        }
        // ucur bizi sıkati
        xhr.open("POST", url, true);
        
        // server dan response json
        // formdata ile upload yapiyorsak contentype boş olmalı
        if( typeof data.append != 'function' ){
        	// xhr.setRequestHeader("Content-Type", "application/json");
        	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        	// xhr.setRequestHeader("Content-Type", "multipart/form-data");
        }
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        // console.log( data );
        // gonder
        xhr.send(data);
    }
}

var AHAJAX_V3_TEXT = {
	req: function( url, data, cb ){
		var xhr;
		// DT_functions.row_loader(true);
        // modern browserlar da XMLHttpRequest kullan
        if(typeof XMLHttpRequest !== 'undefined') {
        	xhr = new XMLHttpRequest();
        } else {
            // IE 6 dayinin ibneliklerini çözüyoruz
            var versions = ["MSXML2.XmlHttp.5.0",  "MSXML2.XmlHttp.4.0", "MSXML2.XmlHttp.3.0", "MSXML2.XmlHttp.2.0", "Microsoft.XmlHttp"]        
            // uyan versiyonu kullaniyoruz tek tek kontrol edip
            for(var i = 0, len = versions.length; i < len; i++) {
            	try {
            		xhr = new ActiveXObject(versions[i]);
            		break;
            	}
            	catch(e){}
            }
        }
        // ajax requesti yaptiginda onreadystatechange e tanimlanmis
        // fonksiyon 5 defa calisacak. her calismada durum ile ilgili bilgiye
        // gore fail, complete fonksiyonlari calistirabiliyoruz
        xhr.onreadystatechange = state_check;
        // kontrol fonksiyonu
        function state_check() {
            // uninitialized, loading, loaded, interactive state lerinde
            // birsey yapma requeste devam
            if(xhr.readyState < 4) {
            	return;
            };
            // network level de bir hata olursa calisiyor. ( cross domain vs )
            xhr.onerror = function(){ console.log( "Ajax request failed" ); };
            // readyState 4 request completed
            // status 200 HTTP OK
            if( xhr.readyState === 4 && xhr.status === 200 ) {
                // IE.7 ve altinda json.parse yok
           		// crockford dayinin kutuphanesini kullanabilirim 
           		// var rsp = JSON.parse(xhr.responseText);
           		if( typeof cb == 'function' ) cb( xhr.responseText );
           		// DT_functions.row_loader(false);
                // console.log(xhr);
            } else if( xhr.status >= 500 || xhr.status <= 599 ){
            	console.log( "AJAX Server hatası.");
            	cb( false );
            } 
        }
        // ucur bizi sıkati
        xhr.open("POST", url, true);
        
        // server dan response json
        // formdata ile upload yapiyorsak contentype boş olmalı
        if( typeof data.append != 'function' ){
        	// xhr.setRequestHeader("Content-Type", "application/json");
        	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        	// xhr.setRequestHeader("Content-Type", "multipart/form-data");
        }
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        // console.log( data );
        // gonder
        xhr.send(data);
    }
}


// Slider - Obarey Inc 2016
var Slider = function( element, items, options ){
	this.init = function(){
		this.slides = [];
		this.thumbs = [];
		var tli, sli, imga, img, mob_desc, div_bullet, div_tc, span_title, span_desc,
			slides_cont = document.createElement("ul"),
			thumbs_cont = document.createElement("ul");
		slides_cont.className = "slides";
		addClass( slides_cont, "clearfix" );
		thumbs_cont.className = "slider-thumbs";
		addClass( thumbs_cont, "clearfix" );
		element.appendChild( slides_cont );
		element.appendChild( thumbs_cont );
   		// html olustur
   		for( var i = 0; i < items.length; i++ ){
   			var item = items[i];
   			sli = document.createElement("li");
   			tli = document.createElement("li");
   			imga = document.createElement("a");
   			imga.href = "";
   			img = document.createElement("img");
   			img.src = "res/img/slider/test"+item.test+".png";
   			img.alt = item.title;
   			mob_desc = document.createElement("div");
   			mob_desc.className = "mobile-slider-desc";
   			set_html( mob_desc, item.desc );
   			imga.appendChild( img );
   			imga.appendChild( mob_desc );
   			sli.appendChild( imga );
   			div_bullet = document.createElement("div");
   			div_bullet.className = "custom-bullet";
   			div_tc = document.createElement("div");
   			div_tc.className = "thumb-content";
   			span_title = document.createElement("span");
   			span_title.className = "thumb-title";
   			set_html( span_title, item.title );
   			span_desc = document.createElement("span");
   			span_desc.className = "thumb-desc";
   			set_html( span_desc, item.desc );
   			div_tc.appendChild( span_title );
   			div_tc.appendChild( span_desc );
   			tli.appendChild( div_bullet );
   			tli.appendChild( div_tc );
   			slides_cont.appendChild( sli );
   			thumbs_cont.appendChild( tli );
            // slide ve thumblari listele    al
            this.slides.push( sli );
            this.thumbs.push( tli );
        }
        foreach( this.thumbs, function(thumb){
   		// thumb sigdirma
   			css(thumb, { width : ( 100 / items.length ) + "%"  });
   		});
        addClass( this.slides[0], "active" );
        addClass( this.thumbs[0], "active" );
        this.current_index = 0;
        var slider_ref = this;
        foreach( this.thumbs, function( thumb ){
        	add_event( thumb, "click", function(){
        		slider_ref.click( this );
        	});
        });
    },
    this.click = function( clicked_thumb ){
    	var index = get_node_index( clicked_thumb );
   		// zaten aktifs slide a basılmışsa islem yapmiyoruz
   		if( index == this.current_index ) return false;
   		// bir önceki seçileni kapat
   		removeClass( this.thumbs[this.current_index], "active" );
   		removeClass( this.slides[this.current_index], "active" );
   		// seçilen slaytı aktif yap
   		addClass( clicked_thumb, "active" );
   		addClass( this.slides[get_node_index( clicked_thumb )], "active" );
   		// son state
   		this.current_index = get_node_index( clicked_thumb );
   	}
}

function Obarey_Tooltip(type, data, elem, e){
    var t = $AH("obarey-tooltip");
    if( type == "img" ){
        data = '<div><img src="http://ahsaphobby.net/bus/res/img/rolling.gif" id="tooltip_loader" /></div><img id="ahimage" style="display:none" src="'+data+'" />';
        t.innerHTML = data;
        $AH('ahimage').onload = function(){
            if( this.complete ){
                remove_elem($AH('tooltip_loader'));
                show( $AH('ahimage'));
            }
        };   
    } else if( type == "text"){
        data = '<div class="tooltip-text">'+data+'</div>';
         t.innerHTML = data;
    }
    
   
    t.style.left = ( e.pageX + 20 ) + "px";
    t.style.top  = ( e.pageY + 20 ) + "px";
    t.style.display  = "block";

    elem.onmouseout = function(){
                
        t.style.display  = "none";
        t.style.left = 0 + "px";
        t.style.top  = 0 + "px";
        t.innerHTML = "";
    }

    event_stop_propagation(e);
}


var Filo_Senkronizasyon = {

	BOLGELER: { A:"dk_oasa", B:"dk_oasb", C:"dk_oasc" },
	REFRESH_INTERVAL_FREKANS: 5000,
	DB_KAYDET_INTERVAL_FREKANS: 1000,
	OTO_REFRESH_FREKANS: 100,//100, // server la pc nin saat farkından saçmalıyo mal 400 -> 4.25dk
	SOFOR_VERI_FREKANS: 150,
	REQUEST_COUNTER:0,
	TOTAL_REQ_COUNT:0,
	SENKRONIZASYON_YAPILIYOR: false,
	ITEM_DATA: {},
	FORM_DATA: new FormData(),
	STATUS: "",
	DB_KAYDET_INTERVAL:false,
	REFRESH_INTERVAL: false,
	OTOBUSLER: {},
	SON_UNIX:0,
	GUNCELLE_FLAG: false,
	REFRESH_AFTER_CB: false,
	MANUEL_TETIK_FLAG: false,
    HATLAR: [],
	
	INIT: function( otobus_data,  son_unix ){
		this.OTOBUSLER = otobus_data;
		this.SON_UNIX = son_unix;
		this.REFRESH_INTERVAL = setInterval(this.Interval_Kontrol,this.REFRESH_INTERVAL_FREKANS);
	},
	// filo plan izlemede kendimiz manuel yapiyoruz seçili otobüsleri alması ve
	// tablolari guncellemek icin
	Manuel_Tetik: function( otobus_data, frekans, cb ){
		clearInterval( this.REFRESH_INTERVAL );

		this.OTOBUSLER = otobus_data;
		this.OTO_REFRESH_FREKANS = frekans;
		this.SON_UNIX = Math.floor(Date.now()/1000);
		this.MANUEL_TETIK_FLAG = true;
		this.REFRESH_INTERVAL = setInterval(this.Interval_Kontrol,this.REFRESH_INTERVAL_FREKANS);
		this.REFRESH_AFTER_CB = cb;
	},
	ORER_Refresh: function( otobus_data, logtype ){
        console.log("Güncelleniyor" );
        show($AH('header-loader'));
        clearInterval( this.REFRESH_INTERVAL );
        this.OTOBUSLER = otobus_data;
        this.DB_KAYDET_INTERVAL = setInterval(this.DB_Kaydet, this.DB_KAYDET_INTERVAL_FREKANS);
        this.FORM_DATA = new FormData();
        this.ITEM_DATA = {};
        this.SENKRONIZASYON_YAPILIYOR = true;
        this.FORM_DATA.append('type', 'filo_orer_senkronizasyon');
        this.FORM_DATA.append('log_type', logtype );
        // her bolge icin, tum hatlarin bilgileri alinacak
        for( var bolge in this.OTOBUSLER ){
            this.ITEM_DATA[bolge] = {};
            // her hat icin veriyi filodan alip listeye ekliyoruz
            for( var i = 0; i < this.OTOBUSLER[bolge].length; i++ ){
                this.ORER_Request( i, bolge );
                this.TOTAL_REQ_COUNT++;
            }
        }
	},

	ORER_Request: function( index, bolge ){
		var TRDATA = [];
        var status_str = "Bölge: " + bolge + " / " + " KapıNo: " + this.OTOBUSLER[bolge][index];
        //console.log(status_str + " veri isteği yapılıyor..." );
        this.ITEM_DATA[bolge][this.OTOBUSLER[bolge][index]] = [];
        // filoya bolge ve kapi no ile istek yapiyoruz
        AHAJAX_V3_TEXT.req( "http://ahsaphobby.net/otobus/iett/filo_veri_download/request.php", manual_serialize({ type:'filo_orer_guncelle', bolge: bolge, kapi_no:this.OTOBUSLER[bolge][index]}), function(res){
            //console.log( status_str + " veri alındı, işleniyor..");  
            // her istekte rowlari tuttugumuz array i resetliyoruz


            if( !res ){
            	// console.log('Error callback baslat - ORER Req');
            	Filo_Senkronizasyon.ORER_Request(index, bolge);
            }


            TRDATA = [];
            // gelen veriyi dive aldık islem yapmak icin
            set_html( $AH('senkronizasyon_container'), res );
            // row un altindaki td ler
            var tr = find_elem( $AH('senkronizasyon_container'), "tbody" ).childNodes;

            if( tr == undefined ) Filo_Senkronizasyon.ORER_Request(index, bolge);

            // her bir td nin tuttugu veriyi alip listeliyoruz
            for( var j = 0; j < tr.length; j++ ){
                // td lerin 3 ayri class i var herhangi birine uyani aliyoruz( text node lari almamak icin )
                if( hasClass( tr[j], "yazid") || hasClass(tr[j], "yazim" )|| hasClass(tr[j], 'yazit') || hasClass(tr[j], "yazik") || hasClass(tr[j], "yazi")){
                // kriterimize uyan tum tdleri listeledik
                    TRDATA.push( tr[j] );
                }
            }   
            // console.log(res);
            // simdi td lerimizi filtreleyip, istedigimiz verileri aktif kapi_no lu otobuse prop olarak ekliyoruz
            var nodes;
            if( TRDATA.length > 0 ) {
                // deneyerek buldum hangi veri hangi node da
                for( var x = 0; x < TRDATA.length; x++ ){
                    nodes = TRDATA[x].childNodes;
                    var trstr = TRDATA[x].innerHTML;
                    var hat = nodes[1].innerText.trim();
                    if( hat.indexOf( "*" ) > -1 ){
                    	hat = hat.substr(1);
                    	hat = hat.substr( 0, hat.indexOf("*") );
                    } else if( hat.indexOf("!") > -1 ){
                    	hat = hat.substr( 1);
                    	hat = hat.substr( 0, hat.indexOf("!") );
                    } else if( hat.indexOf("#") > -1 ){
                    	hat = hat.substr(1);
                    	hat = hat.substr( 0, hat.indexOf("#") );
                    }

                    var guzergah = nodes[3].innerText.trim();
                    if( guzergah.indexOf(" ") > -1 ) guzergah = guzergah.substr( 0, guzergah.indexOf(" ") );
                    Filo_Senkronizasyon.ITEM_DATA[ bolge ][Filo_Senkronizasyon.OTOBUSLER[bolge][index]].push( JSON.stringify({
                        no: nodes[0].innerText.trim(),
                        hat: hat,
                        servis: nodes[2].innerText.trim(),
                        guzergah: guzergah,
                        oto: nodes[4].innerText.trim().substr( 2 ),
                        surucu: nodes[5].innerText.trim().substr(1),
                        gelis: nodes[6].innerText.trim(),
                        orer: nodes[7].innerText.trim(),
                        amir: nodes[8].innerText.trim(),
                        gidis: nodes[9].innerText.trim(),
                        tahmin: nodes[10].innerText.trim(),
                        bitis: nodes[11].innerText.trim(),
                        durum_kodu: nodes[13].innerText.substr( 6 ).trim(),
                        sure: trstr.substr( trstr.indexOf('Sefer süresi:') +14 , trstr.indexOf('dk.') - trstr.indexOf('Sefer süresi:') - 14 ).trim(),
                        durum: nodes[12].innerText.trim()
                    }));
                }
            } else {
                Filo_Senkronizasyon.ITEM_DATA[ bolge ][Filo_Senkronizasyon.OTOBUSLER[bolge][index]] = "BOS";
            }
            Filo_Senkronizasyon.Sofor_Veri_Request( index, bolge );
            
        });
	},

	ORER_Sefer_Takip: function( kapi_no, cb ){
		show($AH('header-loader'));
		var TRDATA = [],
			bolge = this.BOLGELER[kapi_no.substr(0, 1)],
        	status_str = "Bölge: " + bolge + " / " + " KapıNo: " + kapi_no;
        //console.log(status_str + " veri isteği yapılıyor..." );
        // filoya bolge ve kapi no ile istek yapiyoruz
        AHAJAX_V3_TEXT.req( "http://ahsaphobby.net/otobus/iett/filo_veri_download/request.php", manual_serialize({ type:'filo_orer_guncelle', bolge: bolge, kapi_no : kapi_no }), function(res){

        	if( !res ){
            	console.log('Error callback baslat - Sefer Takip');
            	// Filo_Senkronizasyon.DB_Kaydet();
            }

            //console.log( status_str + " veri alındı, işleniyor.." );  
            // her istekte rowlari tuttugumuz array i resetliyoruz
            TRDATA = [];
            // gelen veriyi dive aldık islem yapmak icin
            set_html( $AH('senkronizasyon_container'), res );
            // row un altindaki td ler
            var tr = find_elem( $AH('senkronizasyon_container'), "tbody" ).childNodes;

            if( tr != undefined ){
                // her bir td nin tuttugu veriyi alip listeliyoruz
                for( var j = 0; j < tr.length; j++ ){
                    // td lerin 3 ayri class i var herhangi birine uyani aliyoruz( text node lari almamak icin )
                    if( hasClass( tr[j], "yazid") || hasClass(tr[j], "yazim" )|| hasClass(tr[j], "yazik") || hasClass(tr[j], "yazi")){
                    // kriterimize uyan tum tdleri listeledik
                        TRDATA.push( tr[j] );
                    }
                }   
                //console.log(res);
                // simdi td lerimizi filtreleyip, istedigimiz verileri aktif kapi_no lu otobuse prop olarak ekliyoruz
                var nodes;
                if( TRDATA.length > 0 ) {
                    // deneyerek buldum hangi veri hangi node da
                    for( var x = 0; x < TRDATA.length; x++ ){
                        nodes = TRDATA[x].childNodes;
                        if( nodes[12].innerText != 'A' ) continue;
                        if( nodes[3].innerHTML.indexOf('Durak izdusumu') > -1 ){
                            var durak_izdusumu = nodes[3].innerHTML.substr( nodes[3].innerHTML.indexOf('Durak izdusumu: ') + 16, nodes[3].innerHTML.indexOf(' ve ilk Durak') - nodes[3].innerHTML.indexOf('Durak izdusumu: ') - 16 );
                        }
                        if( nodes[7].innerHTML.indexOf( 'Sonraki Sefer Saati') > -1 ){
                            var beklenen_bitis = "Beklenen ortalama tamamlama saati: " + nodes[7].innerHTML.substr( nodes[7].innerHTML.indexOf('Beklenen ortalama tamamlama saati  :') +36 , nodes[7].innerHTML.indexOf('Beklenen ortalama tamamlama saati  :') +41 - nodes[7].innerHTML.indexOf('Beklenen ortalama tamamlama saati  :') -36 );
                        }
                    }
               }
               if( durak_izdusumu == undefined ) durak_izdusumu = "Veri yok.";
               if( beklenen_bitis == undefined ) beklenen_bitis = "Veri yok.";
               // console.log( kapi_no +' Durak İzdüşüm : ' + durak_izdusumu );
               cb( durak_izdusumu, beklenen_bitis );
               hide($AH('header-loader'));
            }
        });
	},
	ORER_Harita_Takip_Refresh: function( kapi_nolar ){
		

	},
	ORER_Harita_Takip_Request: function(){

	},

	Sofor_Veri_Request: function( index, bolge ){
		AHAJAX_V3_TEXT.req( "http://ahsaphobby.net/otobus/iett/filo_veri_download/request.php", manual_serialize({ type:'sofor_bilgileri_al', bolge: bolge, kapi_no:this.OTOBUSLER[bolge][index]}), function(res){
			if( !res ){
            	// console.log('Error callback baslat - ORER Req');
            	Filo_Senkronizasyon.Sofor_Veri_Request(index, bolge);
            }
            // asenkron oldugu icin ayri ayri divler aciyorum çakışma olmasın diye
			var test_div = document.createElement('DIV');
			test_div.className = 'senk_'+Filo_Senkronizasyon.OTOBUSLER[bolge][index];
			$AH('senkronizasyon_sofor_container').appendChild( test_div );
			set_html( test_div, res );
			var tr = find_elem( test_div, "tbody" ).childNodes;
			var veri = tr[1].childNodes[1].innerText.substr(3),
				sof_array = veri.split(" "),
				sicil_no = sof_array[0],
				tel = sof_array[sof_array.length - 1],
				surucu = "",
				item;
			tel = tel.substr(1);
			tel = tel.substr(0, tel.length -1 );
			sof_array.splice(0,1);
			sof_array.splice(sof_array.length-1, 1);
			for( var x = 0; x < Filo_Senkronizasyon.ITEM_DATA[ bolge ][Filo_Senkronizasyon.OTOBUSLER[bolge][index]].length; x++ ){
				surucu = "";
				item = JSON.parse(Filo_Senkronizasyon.ITEM_DATA[ bolge ][Filo_Senkronizasyon.OTOBUSLER[bolge][index]][x]);
				for( var j = 0; j < sof_array.length; j++ ) surucu += " " + sof_array[j];
				item.surucu = surucu.trim();
				item.surucu_tel = tel.trim();
				item.surucu_sicil_no = sicil_no.trim();
				Filo_Senkronizasyon.ITEM_DATA[ bolge ][Filo_Senkronizasyon.OTOBUSLER[bolge][index]][x] = JSON.stringify( item );
			}
			remove_elem( test_div );
			Filo_Senkronizasyon.REQUEST_COUNTER++;
		});	


	},
	Sofor_Veri_Refresh: function(){

	},
	DB_Kaydet: function(){
		
		 if( Filo_Senkronizasyon.SENKRONIZASYON_YAPILIYOR ){
            if( Filo_Senkronizasyon.REQUEST_COUNTER > 0 && Filo_Senkronizasyon.REQUEST_COUNTER == Filo_Senkronizasyon.TOTAL_REQ_COUNT ){
                Filo_Senkronizasyon.FORM_DATA.append("items", JSON.stringify( Filo_Senkronizasyon.ITEM_DATA) );
                console.log( "Veritabanına kaydediliyor..");
                AHAJAX_V3.req( Base.AJAX_URL + 'filo_senkronizasyon.php', Filo_Senkronizasyon.FORM_DATA, function(res){

                	if( !res ){
                		console.log('Error callback baslat - DB_Kaydet');
                		Filo_Senkronizasyon.DB_Kaydet();
                	}

                    //console.log( "Senkronizasyon tamamlandı.");
                    console.log( "VT Güncel");
                    console.log(res);

                    // tetik manuelsi son unix an olacak
                    if( Filo_Senkronizasyon.MANUEL_TETIK_FLAG ) {
                    	Filo_Senkronizasyon.SON_UNIX = Math.floor(Date.now()/1000);
                    } else {
                    	Filo_Senkronizasyon.SON_UNIX = res.son_unix;
                    }
                    Filo_Senkronizasyon.REFRESH_INTERVAL = setInterval(Filo_Senkronizasyon.Interval_Kontrol,Filo_Senkronizasyon.REFRESH_INTERVAL_FREKANS);
                    //css($AH('guncelle'), { display:'inline-block'});
                    //location.reload();
                    hide($AH('header-loader'));
                    if( typeof Filo_Senkronizasyon.REFRESH_AFTER_CB === 'function' ) Filo_Senkronizasyon.REFRESH_AFTER_CB(Filo_Senkronizasyon.ITEM_DATA);
                });
                console.log(Filo_Senkronizasyon.ITEM_DATA);
                clearInterval( Filo_Senkronizasyon.DB_KAYDET_INTERVAL );
                Filo_Senkronizasyon.REQUEST_COUNTER = 0;
                Filo_Senkronizasyon.TOTAL_REQ_COUNT = 0;
                Filo_Senkronizasyon.SENKRONIZASYON_YAPILIYOR = false;
                set_html($AH('senkronizasyon_container'), "");
            }
        }
	},
	Interval_Kontrol: function(){
		console.log('Güncelleme kontrolü ( Kalan süre : ' + ( Filo_Senkronizasyon.OTO_REFRESH_FREKANS - (Math.floor(Date.now()/1000) - Filo_Senkronizasyon.SON_UNIX) ) + ' saniye )');
        if( Math.floor(Date.now()/1000) - Filo_Senkronizasyon.SON_UNIX >= Filo_Senkronizasyon.OTO_REFRESH_FREKANS ) Filo_Senkronizasyon.ORER_Refresh( Filo_Senkronizasyon.OTOBUSLER, 'komple' ); 
	}


};
// SectionTab - Obarey Inc. 2016
var SectionTab = function( element, items, options ){
	this.init = function(){
		this.page_count = 1;
		this.active_page = 1;
		this.active_rows = [];
		this.section_rows = [];
		var sec_row = create_element( "div", [ "section-row", "clearfix" ] ),
			gal_item, gal_item_container, gal_item_thumb, gal_item_content, gal_item_title, sec_count = 1;
        // html olustur
        for( var i = 0; i < items.length; i++ ){   
        	gal_item = create_element( "div", ["gal-item"] );
        	gal_item_container = create_element("div", ["gal-item-container"]);
        	gal_item_thumb = create_element( "div", ["gal-item-thumb"]);
        	gal_item_content = create_element( "div", ["gal-item-content"]);
        	gal_item_thumb.appendChild( create_img( items[i].img, items[i].title ) );
        	gal_item_title = create_element( "span", [ "gal-item-title"] );
        	set_html( gal_item_title, items[i].title );
        	gal_item_content.appendChild(gal_item_title  );
        	gal_item_content.appendChild( this.create_tag_span( items[i].tags ) );
        	gal_item_container.appendChild( gal_item_thumb );
        	gal_item_container.appendChild( gal_item_content );
        	gal_item.appendChild( gal_item_container );
        	sec_row.appendChild( gal_item );
            // item click
            add_event( gal_item, "click", function(){
            	window.location = items[i].href;
            });
            // her dortte bir yeni row olustur
            if( sec_count == 4 || i == items.length - 1 ){
            	element.appendChild( sec_row );
            	this.section_rows.push( sec_row );
            	sec_row = create_element( "div", [ "section-row", "clearfix" ] );
            	sec_count = 0;
            }
            sec_count++;
        }
        this.total_row_count = this.section_rows.length;
        this.page_count = Math.ceil( this.total_row_count / options.row_count );
        for( var i = 0; i < options.row_count; i++ ) {
        	addClass( this.section_rows[i], "active" );
        	this.active_rows.push( i );
        }
        this.prev_buton = find_elem( element.parentNode, ".section-tab-prev" );
        this.next_buton = find_elem( element.parentNode, ".section-tab-next" );
        // tek sayfaysa butonlari gizle
        if( this.page_count == 1 ) {
        	hide( this.prev_buton );
        	hide( this.next_buton );
        } else {
            // butonlar varsa event attach et
            var section_ref = this;
            add_event( this.next_buton, "click", function(e){
            	section_ref.next_page();
            });
            add_event( this.prev_buton, "click", function(e){
            	section_ref.prev_page();
            });
        }
    },
    this.next_page = function(){
    	var start;
               /*  aktif rowlar = [ 3, 4 , 5 ]
                *  opt.row_count = 3
                *  for( i = 3; i < 3+3 ) 3, 4, 5 */

        for( var i = this.active_rows[0]; i < this.active_rows[0] + options.row_count ; i++ ){
        	removeClass( this.section_rows[i], "active" );
        }
        this.active_rows = [];

        if( this.active_page == this.page_count ){
        	this.active_page = 1;
        	for( var i = 0; i < options.row_count; i++ ) {
        		addClass( this.section_rows[i], "active" );
        		this.active_rows.push( i );
        	}
        } else {
                  
                  /*  active_page = 2
                   *  total_row_count = 9
                   *  aktif_rowlar = [ 5, 6, 7, 8 ]
                   *  opt.row_count = 5
                   *  start = 2 * 5 - 5 = 5
                   *  for( i = 5; i < 5 + 5 ) 5, 6, 7, 8 ( fazlaysa kesiyorum ) */
                  
            this.active_page++;
            start = this.active_page * options.row_count - options.row_count;
            for( var i = start; i < start + options.row_count; i++ ){
                // son sayfa tam dolmuyorsa fazladan 
                if( i < this.total_row_count ){
                	addClass( this.section_rows[ i ], "active" );
                	if( i < this.total_row_count ) this.active_rows.push( i );
                }
            }
        }

    },
    this.prev_page = function(){
    	var start;
    	for( var i = this.active_rows[0]; i < this.active_rows[0] + options.row_count ; i++ ){
    		removeClass( this.section_rows[i], "active" );
    	}
        // temizle aktifleri
        this.active_rows = [];
        if( this.active_page == 1 ){
            // ilk sayfadaysa prev yapildiginda son sayfaya git
            this.active_page = this.page_count;
                  /*  aktif rowlar = [ 0, 1, 2, 3, 4 ]
                   *  opt.row_count = 5
                   *  total_row = 9
                   *  start = 9 - 5 + 1 = 5
                   *  for( i = 5; i < 5 + 5 ) 6, 7, 8, 9 */
            start = this.total_row_count - options.row_count + 1;
            for( var i = start; i < start + options.row_count; i++ ) {
            	addClass( this.section_rows[i], "active" );
            	this.active_rows.push( i );
            }
        } else {

                  /*  active_page = 1
                   *  total_row_count = 9
                   *  aktif_rowlar = [ 6, 7, 8, 9 ]
                   *  opt.row_count = 5
                   *  start = 1 * 5 - 5 = 0
                   *  for( i = 0; i < 0 + 5 ) 0, 1, 2, 3, 4 */

            this.active_page--;
            start = this.active_page * options.row_count - options.row_count;
            for( var i = start; i < start + options.row_count; i++ ){
            	addClass( this.section_rows[ i ], "active" );
            	if( i < this.total_row_count ) this.active_rows.push( i );
            }
        }
    },
    this.create_tag_span = function( tags ){
    	var container = create_element("span", ["gal-item-tags"]), a, i;
    	foreach( tags, function(tag){
    		a = document.createElement( "a" );
    		a.href = "?tag="+tag;
    		a.className="tag";
    		i = document.createElement("i");
    		i.className = "tag";
    		set_html( i, tag );
    		a.appendChild(i);
    		container.appendChild( a );
    	});
    	return container;
    }
}

// right bar tab
var jwTab = function( options ){
	this.init = function(){
		this.bullets = find_elem( options.container, ".tab-button" );
		this.divs = find_elem( options.container, "[tabdiv]" );
		this.active_index = 0;
	    // ilk siradaki tabi aktif et
	    addClass( this.bullets[0], "selected" );
	    addClass( this.divs[0], "active" );
	    var this_ref = this;
	    add_event( this.bullets, "click", function(ev){
	    	this_ref.activate(this);
	    });
	},
	this.activate = function( bullet ){
	   	// tab bulletlerden aktive etme
	   	if( bullet.getAttribute("tab-toggle") == null ){
	   		var bullet_index = get_node_index( bullet.parentNode );
	   		if( bullet_index != this.active_index ){
	   			removeClass( this.divs[this.active_index], "active" );
	   			removeClass( this.bullets[this.active_index], "selected" );
	   			addClass( this.divs[bullet_index], "active" );
	   			addClass( this.bullets[bullet_index], "selected" );
	   			this.active_index = bullet_index;
	   		}
	    	// tab bullet harici attr lerden aktive etme
	    } else {
	    	for( var i = 0; i < this.divs.length; i++ ){
	    		if( this.divs[i].getAttribute("tabdiv") == bullet.getAttribute("tab-toggle") ){
	    			removeClass( this.divs[this.active_index], "active" );
	    			removeClass( this.bullets[this.active_index], "selected" );
	    			addClass( this.divs[i], "active" );
	    			addClass( this.bullets[i], "selected" );
	    			this.active_index = i;
	    			continue;
	    		}
	    	}
	    }
	}
}

function clear_select_options( select ){
	select.options.length = 0;
}

function add_select_option( select, val, text, clear ){
	if( clear ) clear_select_options(select);
	var option = document.createElement("option");
	option.text = text;
	option.value = val;
	select.add(option);
}

var FormValidation = {
	errors: [],
	list: [],
	form_prefix: "",
	error_messages: {
		posnum: "Numerik veya sıfırdan büyük olmalıdır.",
		req: "Boş bırakılamaz.",
		not_zero: "Sıfırdan büyük olmalıdır.",
		email: "Lütfen geçerli bir eposta adresi girin.",
		select_no_zero : "Boş bırakılamaz."
	},
	submit_btns:[],
	find_inputs: function (f){
			// Listeyi her kontrol Ã¶ncesi bosalt
			this.list = [];
			this.form_prefix = f.id;
			var form = f, i;
			// ilk versiyonda tum inputlari listeliyordum
			//artik kontrol class i olanlari aliyoruz sadee

			for( i = 0; i <= form.elements.length; i++ ){
				if( form.elements[i] != undefined ) {
					if( 
						hasClass( form.elements[i], "posnum" ) ||
						hasClass( form.elements[i], "req" )  ||
						hasClass( form.elements[i], "not_zero" )  ||
						hasClass( form.elements[i], "email" ) ||
						hasClass( form.elements[i], "select_no_zero")
						) {
						if( form.elements[i].type == "text" ||
							form.elements[i].type == "textarea" ||
							form.elements[i].type == "password" ||
							form.elements[i].type == "email" ||
							form.elements[i].type == "select-one" ||
							form.elements[i].type == "select-multiple" ||
							form.elements[i].type == "checkbox"
							) this.list.push( form.elements[i] );
						// Radio secildiyse
					if( form.elements[i].type == "radio" ){
						if( form.elements[i].checked ) this.list.push( form.elements[i] );
					}
				}
					// submit btn
					if( form.elements[i].type == "submit" ) this.submit_btns.push( form.elements[i] );
				}
			}
			// this.keyup( f );
		},
	check: function(f){
		this.find_inputs(f);
		this.check_input( this.list );
		if( this.is_valid() ) {
			return true;
		} else {
			this.show_errors();
			this.keyup(f);
			// return false;
		}
	},
	custom_check: function( elem, text, check_func ){
		if( !check_func(elem.value) ){
			if( !hasClass( elem, "redborder" ) ){
				addClass( elem, "redborder" );
				error_div = create_element( "div", ["input-error"] );
				set_html( error_div, text );
				elem.parentNode.appendChild( error_div );
			}
			return false;
		}
		return true;
	},
	// form submit esnasinda tum submit butonlari disabled yap
	// birden fazla olabilir submit o yuzden array
	get_input_list: function(){
		return this.list;
	},
	check_input: function(input){
		// Toplu kontrol
		var elem, i, x;
		input_count = input.length;
		// gelen inputlarin sayisini inputlar array halinde geldiginde aliyoruz
		// eger tek bir input gelirse length = undefined oluyor. buradan tek input geldigini anlayip
		// loop icin son limiti 1 yapiyoruz yani bir kere loop yapiyor.
		if( input_count == undefined ) input_count = 1;
		for( i = 0; i < input_count; i++ ){
			// burada da input tekse direk onu loop ta isleme aliyoruz
			// eger liste halinde geldiyse, listenin elemanlarini tek tek isliyoruz
			( input instanceof Array ) ? elem = input[i] : elem = input;
			for( x in this.error_messages ){
				if( hasClass( elem, x ) ){
					// ornek -> this.posnum( val )
					if( !this[x]( elem.value ) ) this.errors.push( [ elem, this.error_messages[x] ] );
				}
			}
		}
	},
	is_valid: function(){
		return ( this.errors.length == 0 );
	},
	show_serverside_errors: function( errors ){
		var error_div;
		for( var i = 0; i < this.list.length; i++ ){
			var error_index = (this.list[i].id).substr( this.form_prefix.length + 1 );
			if( errors[ error_index ] != undefined ){

				if( !hasClass( this.list[i], "redborder" ) ){
					// html = '<div class="input-error">'+errors[ error_index ]+'</div>';
					// addClass( this.list[i], "redborder" );
					// append_html( this.list[i].parentNode, html );	

					addClass( this.list[i], "redborder" );
					error_div = create_element( "div", ["input-error"] );
					set_html( error_div, errors[ error_index ] );
					this.list[i].parentNode.appendChild( error_div );

				}
			}
		}
		// form prefix formun id si, keyup u burda yapiyoruz
		this.keyup($AH(this.form_prefix));
	},
	show_errors: function(){
		var co = this.errors.length, html = "", error_div;
		for( var i = 0; i < co; i++ ){
			if( !hasClass( this.errors[i][0], "redborder" ) ){
				addClass( this.errors[i][0], "redborder" );
				error_div = create_element( "div", ["input-error"] );
				set_html( error_div, this.errors[i][1] );
				this.errors[i][0].parentNode.appendChild( error_div );
			}	
		}
		// Hatalari gosterdikten sonra bosalt
		// Bir önce kontrol edilen formun hatalarindan kurtulmak
		this.errors = [];
	},
	hide_error: function( e ){
		var p = e.parentNode, pc = p.childNodes, i;
		removeClass(e, "redborder" );
		// input-error divini bul ve sil
		for( i = 0; i < pc.length; i++ ){
			if( pc[i] != undefined ) 
				if( hasClass(pc[i], "input-error") ) {
					p.removeChild(p.childNodes[i]);
				}
			}
		},
	posnum: function( val ){
		// console.log( val );
		// Bos birakilmissa true don, onu kontrol icin req() fonksiyonu var
		if( trim(val) == "") return true;
		return (val - 0) == val && trim( (''+val) ).length > 0 && !( val < 0 );
	},
	not_zero: function( val ){
		return !( val <= 0 );
	},
	req: function( val ){
		return !( trim( val ) == "" || val == undefined );
	},
	select_no_zero: function( val ){
		return ( val != 0 );
	},
	email: function( val ){
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(val);
	},
	// keyuta error gizleme
	keyup: function (form){
		add_event_on( form, ".redborder", "keyup", function(targ, e ){ FormValidation.hide_error( targ ) });
		add_event_on( form, ".redborder", "change", function(targ, e ){ FormValidation.hide_error( targ ) });
	}
};


var Popup = {
	overlay: "popup-overlay",
	popup  : "popup",
	open   : false,
	on: function( data, header, loader ){
		show( $AH(this.overlay) );
		var	i = $AH(this.popup);
		show(i);
		fade_in(i);
		if( loader == undefined ){
			removeClass( $AH(this.popup), "loader" );
		}

		// Once datalari yazdir
		set_html( i,  "<div id='popup-buton' onclick='Popup.off()'>X</div><div id='popup-header'>"+header+"</div><div id='popup-content'>" + data +"</div>");
		// Ãƒâ€“lÃƒÂ§ - ortala
		css( i, {
			//left: "50%",
			//marginLeft:  "-" + ( i.offsetWidth / 2 ) + "px",
			top: ( document.body.scrollTop + 30 ) + "px"
		});
		this.open = true;
	},
	off: function(){
		hide($AH(this.overlay));
		$AH(this.popup).innerHTML = "";
		hide($AH(this.popup));
		this.open = false;
		removeClass( $AH(this.popup), "loader" );
	},
	start_loader:function(){
		addClass( $AH(this.popup), "loader" );
		this.on( '<img src="http://ahsaphobby.net/bus/res/img/rolling.gif" />', "Lütfen bekleyin...", true );
	},
	is_open: function(){
		return this.open;
	}
}

function sefer_hesapla( from, to ){
	if( from == "" || to == "" ) return 0;
	var from_exp = from.split(":"),
		to_exp = to.split(":"),
		from_saat = parseInt(from_exp[0]),
		to_saat = parseInt(to_exp[0]),
		from_dk = parseInt(from_exp[1]),
		to_dk = parseInt(to_exp[1]),
		dakika = 0,
		saat = 0,
		eksi = false;

	if( to_exp.length < 2 || from_exp.length < 2 ) return 0;

	var saat_liste = [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23 ];
	if( from_saat == to_saat ) return to_dk - from_dk;

	if( to_saat < from_saat ){
		var to_dk_temp = to_dk;	
		to_dk = from_dk;
		from_dk = to_dk_temp;
	}


	var ileri = 0, geri = 0;
    for( var x = from_saat; ; x++ ){
        if( x == saat_liste.length ) x = 0;
        if( saat_liste[x] == to_saat ) break;
        ileri++;
    }
    for( var x = from_saat; ; x-- ){
        if( x == -1 ) x = 23;
        if( saat_liste[x] == to_saat ) break;
        geri++;
    }
    var varis;
    if( geri < ileri ){
        varis = geri;
        eksi = true;
    } else if( geri > ileri ){
        varis = ileri;
    } else {
        varis = geri;
    }


    saat = 0;
    dakika += to_dk;
    dakika += 60 - from_dk;
    saat++;
    while( saat != varis ){
        dakika += 60;
        saat++;
    }
    if( eksi ) return dakika*-1;
    return dakika;
}


function manual_serialize( j ){
	var i, s = [], c, str = "";
	if( Object.size(j) > 0 ){
		for( i in j ){
			s.push( i + "=" + j[i] );
		}
		str = s.join("&");
	}
	return str;
}

// uzun stringleri lim_len kadar karakterden sonra ikiye bolup br cakiyoruz arasina
function br_string( string, lim_len ){
	if( string.length > lim_len ){
		return string.substr( 0, lim_len ) + '<br>' + string.substr( lim_len, string.length );
	}
	return string;
}


function serialize(form) {
	if (!form || form.nodeName !== "FORM") {
		return;
	}
	var i, j, q = [];
	for (i = form.elements.length - 1; i >= 0; i = i - 1) {
		if (form.elements[i].name === "") {
			continue;
		}
		switch (form.elements[i].nodeName) {
			case 'INPUT':
			switch (form.elements[i].type) {
				case 'text':
				case 'hidden':
				case 'password':
				case 'email':
				case 'button':
				case 'reset':
				case 'submit':
				q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
				break;
				case 'checkbox':
				case 'radio':
				if (form.elements[i].checked) {
					q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
				}						
				break;
				case 'file':
				break;
			}
			break;			 
			case 'TEXTAREA':
			q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
			break;
			case 'SELECT':
			switch (form.elements[i].type) {
				case 'select-one':
				q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
				break;
				case 'select-multiple':
				for (j = form.elements[i].options.length - 1; j >= 0; j = j - 1) {
					if (form.elements[i].options[j].selected) {
						q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].options[j].value));
					}
				}
				break;
			}
			break;
			case 'BUTTON':
			switch (form.elements[i].type) {
				case 'reset':
				case 'submit':
				case 'button':
				q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
				break;
			}
			break;
		}
	}
	return q.join("&");
}


function confirm_alert( text ){
	var c = confirm( text );
	return c;
}


function fade_in( elem ){
	var op = 0;
	css( elem, { opacity : 0 } );
	function frame() {
		 op+=0.1;
		 elem.style.opacity = op;
		 if (op > 1 && op < 1.1  ) {
		 	clearInterval(id); 
		 }
	}
	var id = setInterval(frame, 10);
}

function get_coords(elem) { // crossbrowser 
    var box = elem.getBoundingClientRect(),
    	body = document.body,
    	docEl = document.documentElement,
    	scrollTop = window.pageYOffset || docEl.scrollTop || body.scrollTop,
    	scrollLeft = window.pageXOffset || docEl.scrollLeft || body.scrollLeft,
    	clientTop = docEl.clientTop || body.clientTop || 0,
    	clientLeft = docEl.clientLeft || body.clientLeft || 0,
    	top  = box.top +  scrollTop - clientTop,
    	left = box.left + scrollLeft - clientLeft;
    return { top: Math.round(top), left: Math.round(left) };
}

// db icin tarihleri ters donduruyorum
function reverse_date( date ){
	var str = date.split('-');
	str.reverse();
	return str.join('-');
}


// var base_li = create_element( "li", [] ),
				// 	item_header  = create_element( "div", ["item-header"] ),
				// 	item_cont = create_element( "div", ["item-container", "clearfix"] ),
				// 	left_side = create_element("div", ["left-side", "clearfix"]),
				// 	preview_cont = create_element("div", ["preview-container", "ov_12_9"]),
				// 	round_border = create_element("div", ["round-border"]),
				// 	preview_img = create_img( "res/temp_upload/hawa.jpg", "Preview" ),
				// 	options_cont = create_element("div", ["options-container"]),
				// 	options_ul = create_element("ul", ["options-list", "clearfix"]),
				// 	navi_cont = create_element( "div", ["navigation-container"]),
				// 	navi_ul = create_element( "ul", [] ),
				// 	navi_ul_li, option_base_li, option_title, opt_ul, opt_li, opt_li_a;

				// // varyantları oluştur	
				// for( var i = 0; i < TestVariants.length; i++ ){
				// 	option_base_li = create_element( "li", [] );
				// 	option_title = create_element( "span", ["option-header"] );
				// 	set_html( option_title, TestVariants[i].title );
				// 	opt_ul = create_element("ul", ["option"]);
				// 	opt_ul.setAttribute("option", TestVariants[i].title);
				// 	for( var x = 0; x < TestVariants[i].options.length; x++){
				// 		opt_li = create_element("li",[]);
				// 		opt_li_a = create_element("a", [] );
				// 		opt_li_a.href="";
				// 		set_html(opt_li_a, TestVariants[i].options[x] );
				// 		opt_li.appendChild(opt_li_a);
				// 		opt_ul.appendChild( opt_li );
				// 	}	
				// 	option_base_li.appendChild(option_title);
				// 	option_base_li.appendChild(opt_ul);
				// 	options_ul.appendChild(option_base_li);
				// }
				// options_cont.appendChild(options_ul);

				// // sag navigasyon butonları
				// var navi_buttons = ["Resmi Yükle", "Listeye Ekle", "Sipariş Notu Ekle", "İptal Et"],
				// 	navi_buttons_icons = ["upload_picture", "add_to_list", "add_note", "cancel"];
				// for( var i = 0; i < navi_buttons.length; i++ ){
				// 	navi_ul_li = create_element("li", [] );
				// 	var li_a = create_element("a", []);
				// 	li_a.appendChild( create_element("i", [navi_buttons_icons[i]] ) );
				// 	append_html(li_a, "#"+navi_buttons[i]);
				// 	navi_ul_li.appendChild(li_a);
				// 	navi_ul.appendChild(navi_ul_li);
				// }
				// // sag preview ve fiyat 
				// var nav_bottom = create_element("div", ["nav-bottom-section"]),
				// 	bottom_prev = create_element("div", ["preview-container"]),
				// 	price_cont = create_element("div", ["price-container"]),
				// 	price_span = create_element("span", [] ),
				// 	price_bold = create_element("span", ["price"]);

				// // sag alt olusturma
				// price_span.appendChild( price_bold );
				// set_html(price_bold, "10 TL");
				// price_cont.appendChild(price_span);
				// bottom_prev.appendChild( create_img( "res/temp_upload/hawa.jpg", "Preview" ) );
				// nav_bottom.appendChild( bottom_prev );
				// nav_bottom.appendChild( price_cont );
				// navi_cont.appendChild( navi_ul );
				// navi_cont.appendChild( nav_bottom );

				// // sol taraf
				// round_border.appendChild( preview_img );
				// preview_cont.appendChild(round_border);
				// left_side.appendChild( preview_cont );
				// left_side.appendChild( options_cont );
				// item_cont.appendChild(left_side);
				// item_cont.appendChild( navi_cont );
				// set_html(item_header, "Oval Seri");
				// // final container
				// base_li.appendChild( item_header);
				// base_li.appendChild( item_cont);	
				// listeye ekle






				 // AHReady( function(){

                //     var tab_divs_container = find_elem( document, ".tab-divs"),
                //         tab_bullets_container = find_elem( document, ".tab-bullets");
                //     for( var i = 0; i < tab_divs_container.length; i++ ){
                //         var div = find_elem( tab_divs_container[i], "li" ),
                //             bullet = find_elem( tab_bullets_container[i], "li" );
                //         addClass( div[0], "selected" );
                //         addClass( bullet[0], "selected" );
                //     }
                //     add_event( $AHC("tab-bullet"), "click", function(){
                //         if( !hasClass(this, "selected")){
                //             var parent = this.parentNode.parentNode.parentNode,
                //                 divs   = find_elem( find_elem( parent, ".tab-divs" ), "li" ),
                //                 bullets   = find_elem( find_elem( parent, ".tab-bullets" ), "li" );
                //             for( var i = 0; i < divs.length; i++ ){
                //                 removeClass( divs[i], "selected");
                //                 removeClass( bullets[i], "selected");
                //             }
                //             addClass( divs[get_node_index(this.parentNode)], "selected" );
                //             addClass( this.parentNode, "selected");
                //         }
                //     });


                // });