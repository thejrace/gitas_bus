
var Base = {
	mobile:false,
	language:"TR",
	main_url: "http://ahsaphobby.net/bus/",
	AJAX_URL: "",
	mode_check: function(){
		if( window.innerWidth <= 767 ){
			this.mobile = true;
		}
	},
	perms:[]
};


AHReady(function(){

	Base.AJAX_URL = Base.main_url + "ajax/";


	add_event( window, "scroll", function(){
		// çok uzunsa popup içeriği kaydırma yapma
		if( Popup.is_open() && window.innerHeight > $AH(Popup.popup).offsetHeight + 30 ) {
			css( $AH(Popup.popup), { top: document.body.scrollTop + 30 + "px" });
		}	
	});

	// adet arttir azalt butonlari
	var qty_btns = find_elem( document, ".qtybtn" );
	if( qty_btns.length > 0 ){
		add_event( qty_btns, "click", function(){

			var input = find_elem( this.parentNode, "input" ),
				val = parseInt(input.value);
			if( hasClass(input, "redborder") ){
				FormValidation.hide_error(input);
			}
			if( hasClass(this,"arttir")){
				input.value = val + 1;
			} else {
				if( val > 0 ) input.value = val - 1;
			}
		});
	}
});
	
	// noktali sayiyi virgullu hale getirip, virgul sonrasi iki basamak birakma
	function virgul_sonrasi_iki_basamak( deger, retfloat ){
		var lira,
		kurus,
		final_deger;
		// kurus varsa 
		if( deger.toString().indexOf(".") > -1 ) {
			lira  = deger.toString().substr( 0, deger.toString().indexOf(".") );
			kurus = deger.toString().substr( deger.toString().indexOf(".") + 1 );
			if( kurus.length > 2 ){
				kurus = kurus.substr(0, 2);
			} else if( kurus.length == 1 ){
				kurus += "0";
			}
			if( retfloat ){
				final_deger = parseFloat(lira + "." +kurus);
			} else {
				final_deger = lira + "," +kurus;
			}
		// kurus yoksa sona iki sifir ekliyoruz
		} else {
			if( retfloat ){
				final_deger = parseFloat(deger);
			} else {
				final_deger = deger + ",00";
			}
		}
		return final_deger;
	}


	// form > div.main-form-notf
	var FormNotf = function( form ){
		
		this.states = [ 'error', 'success', 'loading' ];
		this.last_state = "";
		this.classname = ".main-form-notf";
		this.elem = find_elem(form.parentNode, this.classname );

		this.init = function( state, text ){
			removeClass( this.elem, 'active' );
			if( this.last_state == "" ){
				for( var i = 0; i < this.states.length; i++ ) removeClass( this.elem, this.states[i] );
			} else {
				removeClass( this.elem, this.states[this.last_state] );		
			}
			set_html( this.elem, text );
			addClass( this.elem, this.states[state] ); 
			addClass( this.elem, 'active' );
			this.last_state = state;
		}

	};


	var DataTable = function( options ){
			this.data = options.data, // orjinal veri
			this.record_count = this.data.length;
			this.active_data = [];
			this.rrp = 30;
			this.page = 1;
			this.page_count = Math.ceil(this.record_count/this.rrp);
			this.order = "DESC";
		    this.filtered = false;
		    this.filter_data = {}; // filtreleme kriterleri
		    this.data_filtered = []; // filtrelenmis veri
		    this.active_data = []; // filterelemeye gore aktif kullanilan veri
		    this.init = function(){
		    	var i, html = "", counter = 0, table_html = "";
		    	// filtreleme varsa filtreli listeden hesaplamiyoruz verileri
		    	if( this.filtered ){
		    		this.record_count = this.data_filtered.length;
		        	this.page_count = Math.ceil(this.record_count/this.rrp); 
		        	this.active_data = this.data_filtered;
		    	} else {
		    		this.record_count = this.data.length;
		        	this.page_count = Math.ceil(this.record_count/this.rrp); 
		        	this.active_data = this.data;
		    	}
		         if( this.page == 1 ){
		        	// ilk sayfa -> index 0
		        	i = 0;
		        } else {
		        	// ornek
		        	// 98 kayit olsun
		        	// sayfa 2
		        	// rrp 20
		        	// o zaman listeden almaya baslicagimiz aralik ;
		        	// ( (2 * 20) - 20 )= 20 - 39
		        	i = this.page * this.rrp - this.rrp ;
		        }
		        for( i; i < this.active_data.length; i++ ){
		        	var item = this.active_data[i];
		        	if( counter < this.rrp  ){
			        	var header_title_items = [],
			        		item_header = "", item_content = "", item_navs = "";

			        	// item header 
			        	for( var g = 0; g < options.header_keys.length; g++ ){
			        		header_title_items.push( item[options.header_keys[g]] );
			        	}
			        	item_header = header_title_items.join(" - ");

			        	// adet 0 sa otobuse ekle butonu alert vericek
			        	var adet_data = 1;
			        	// data
			        	for( var head in options.data_headers ){
			        		// datatable initte yazilip, db den gelmeyenleri yazdirmiyoruz
			        		if( item[options.data_headers[head]] != undefined ){
				        		item_content += '<li><span>'+head+'</span>'+item[options.data_headers[head]]+'</li>';
				        		if( head == "Miktar" ) adet_data = parseInt( item[options.data_headers[head]] );
				        	}
			        	}

			        	// eger alt tablo varsa table olusturmaya basliyoruz
			        	if( options.inner_table_contents != undefined ){
			        		// head kısmı
			        		table_html = '<table class="inner-table"><thead><tr>';
			        		// loop ta dondurecegimiz her bir item icin var
			        		var inner_table_item,
			        			// inner_table_contents[options.inner_table_parent_key], aktif item için veri objesi
			        			table_content_index = 0;
			        		for( var u in options.inner_table_contents ){
			        			// loop dondurup eger aktif item icin table content verilmiş mi
			        			// verilmişse index i table_content_index e aliyoruz
			        			if( u == item[options.inner_table_parent_key] ){
			        				table_content_index = u;
			        				break;
			        			}
			        		}

			        		// eger aktif item için contentste veri varsa table html oluşturmaya basliyoruz
			        		if( table_content_index != 0 ){
				        		for( var thead in options.inner_table_thead ){
				        			table_html += '<td>'+options.inner_table_thead[thead]+'</td>';
				        		}
				        		table_html += '</tr></thead><tbody>';
				        		for( var u in options.inner_table_contents[table_content_index] ){
				        			inner_table_item = options.inner_table_contents[table_content_index][u];
				        			table_html += '<tr>';
				        			for( var td in inner_table_item ){
				        				table_html += '<td>'+inner_table_item[td]+'</td>';
				        			}
				        			// tablonun sonundaki nav iconlar
				        			if( options.inner_table_navs != undefined ){
				        				for( var k = 0; k < options.inner_table_navs.length; k++ ){
				        					table_html += '<td item-id="'+inner_table_item[options.inner_table_navs_item_key]+'"><a href="'+options.inner_table_navs[k].href+inner_table_item[options.inner_table_navs_item_key]+'"</a>'+options.inner_table_navs[k].title+'</td>';
				        				}
				        			}
									table_html +='</tr>';
				        		}
				        		table_html += '</tbody></table>';
				        	} else {
				        		// eger aktif item için contents yoksa head ini olusturdugumuz tablonun html i boşaltıyoruz eklemesin diye
				        		table_html = "Veri Yok";
				        	}
			        	}

			        	// alt nav
			        	for( var nav in options.nav_headers ){
			        		// perm kontrol
			        		if( in_array( options.nav_headers[nav].pl, Base.PERMS ) ){
				        		var delete_class = "", item_id = "", onclick = "";
				        		if( nav == 'SİL' ){
				        			delete_class = 'jx-delete';
				        			item_id = 'item-id="'+item.id+'"';
				        		} 
				        		if( nav == 'OTOBÜSE EKLE' && adet_data == 0 ){
				        			onclick = 'onclick="alert(\'Bu malzeme stokta yok.\'); return false;"';
				        		}
				        		item_navs += '<li><a '+onclick+' href="'+options.nav_headers[nav].href+item.id+'" '+item_id+' class="'+options.nav_headers[nav].class+ ' ' +delete_class+'">'+nav+'</a></li>';
			        		}
			        	}

				        html += '<li>'+
								'<div class="otobus-header  clearfix">'+
									'<div class="otobus-summary">'+
										'<i class="'+options.icon_class+'"></i>'+
										'<span>'+item_header+'</span>'+
									'</div>'+
									'<i class="ico down-arrow otobus-arrow"></i>'+
								'</div>'+
								'<div class="otobus-content">'+
									table_html+
									'<ul>'
										+item_content+
									'</ul>'+
									'<div class="content-nav clearfix">'+
										'<ul>'
											+item_navs+
										'</ul>'+
									'</div>'+
								'</div>'+
								'</li>';

							counter++;
						
			        }
			    }
			 	set_html( options.container, html );
			 	this.init_pagination();

			 	fade_in( options.container );
			 	
		    },
		    this.init_pagination = function(){
		    	var rrp_options = [ 1, 2, 5, 30, 50, 100 ],
		    		html = 
		    	'<div class="pagination-col">'+
		    	'<span>Kayıt Sayısı</span>'+
		    	'<select name="dt_rrp" id="dt_rrp" >';
		    	for( var i = 0; i < rrp_options.length; i++ ){
		    		var selected = "";
		    		if( rrp_options[i] == this.rrp ) selected = " selected";
		    		html += '<option '+selected+'>'+rrp_options[i]+'</option>';
		    	}
		    	html +='</select>'+
		    	'</div>'+
		    	'<div class="pagination-col mobile-iblock">'+
		    	'<button  class="pagination-btn first" ></button>'+
		    	'<button  class="pagination-btn prev" ></button>'+
		    	'</div>';

		    	html += '<div class="pagination-col hide-mobile">'+
		    	'<span>Sayfa</span>'+
		    	'<select name="dt_page" id="dt_page" >';

		    	for( var i = 1; i < this.page_count + 1; i++ ){
		    		var selected = "";
		    		if( i == this.page ) selected = " selected";
		    		html +='<option '+selected+'>'+i+'</option>';
		    	}
		    	var to = this.rrp * this.page;
				// eger pagination daki to toplam kayit sayisini gecerse 
				if( (this.rrp * this.page) > this.record_count ) to = this.record_count;
				html += '</select></div><div class="pagination-col hide-mobile">'+
				'<span>( '+(this.rrp * this.page - this.rrp + 1)+' - '+to+' / '+this.record_count+' )</span></div>'+	
				'<div class="pagination-col mobile-iblock">'+
				'<button class="pagination-btn next" ></button>'+
				'<button class="pagination-btn last"></button>'+
				'</div>';

				set_html( options.pagin_container, html );
		    },
		    this.change_rrp = function( rrp ){
				this.page = 1;
				this.rrp = rrp;
		        // rrp degisince sayfa sayisida degisiyor
		        this.page_count = Math.ceil(this.record_count/this.rrp);
		        this.init();
		    },
		    this.change_page = function( page ){
		    	// tek sayfa varsa yapma bi bok
		    	if( this.page_count == 1 ) return;
		    	if( page == 0 ) {
		        	// ilk sayfadan geri gidilirse son sayfayi ac
		        	this.page = this.page_count;
		        } else if ( page > this.page_count ){
		        	// son sayfadan ileri gidilirse ilk sayfayi ac
		        	this.page = 1;
		        } else {
		        	// normal
		        	this.page = page;
		        }
		        this.init();
		    },
		    this.change_order = function(){
		    	if( this.order == "ASC" ){
		    		this.order = "DESC";
		    	} else {
		    		this.order = "ASC";
		    	}
		    },
		    this.init_events = function(){
	   			var this_ref = this;
			   	add_event_on( options.pagin_container, ".next", "click", function(targ, ev){

			   		this_ref.change_page( this_ref.page + 1 );
			   	});
			   	add_event_on( options.pagin_container, ".prev", "click", function(targ, ev){
			   		this_ref.change_page( this_ref.page - 1 );
			   	});
			   	add_event_on( options.pagin_container, ".last", "click", function(targ, ev){
			   		this_ref.change_page( this_ref.page_count );
			   	});
			   	add_event_on( options.pagin_container, ".first", "click", function(targ, ev){
			   		this_ref.change_page( 1 );
			   	});
			   	add_event_on( options.pagin_container, "#dt_rrp", "change", function(targ, ev){
			   		this_ref.change_rrp( targ.value );
			   	});
			   	add_event_on( options.pagin_container, "#dt_page", "change", function(targ, ev){
			   		this_ref.change_page( targ.value );
			   	});
			   	add_event_on( options.container, '.otobus-header', 'click', function(targ,ev){
					toggle_class( find_elem( targ.parentNode, ".otobus-content" ), "active");
					toggle_class( find_elem( targ, ".otobus-arrow"), "up-arrow");
					fade_in( find_elem( targ.parentNode, ".otobus-content" ) );

					// console.log( document.body.parentNode.parentNode.parentNode );

					window.scrollTo(0, get_coords(targ).top);
				});

				add_event( find_elem( options.pagin_container.parentNode, ".pagin-toggle" ), "click", function(){
					toggle_class( find_elem( this.parentNode, ".pagination-center" ), "activeib");
				});

				if( options.filter_container != undefined ){
					add_event( find_elem( options.filter_container.parentNode, ".pagin-toggle" ), "click", function(){
						toggle_class( find_elem( this.parentNode, ".pagination-center" ), "activeib");
					});
				}
				
				if( options.jx_delete != undefined ){
					add_event_on( options.container, '.jx-delete', 'click', function(targ,ev){
						options.jx_delete( targ );
						event_prevent_default(ev);
					});
				}


				// input filtreleme, iki yerde yaptigim icin inner fonksiyon yaptim
				function filter_actions(){
					var filters = {};
					for( var i = 0; i < options.filter_inputs.length; i++ ){
						if( $AH(options.filter_inputs[i]).value != 0 ) filters[options.filter_inputs[i].substr(3)] = $AH(options.filter_inputs[i]).value;
					}
					if( Object.size(filters) > 0 ){
						this_ref.filter( filters );
					} else {
						this_ref.reset();
					}
				}

				//  input a yazip enter a bastıgında da arasin diye form koydum
				if( $AH('filter_form') != undefined ){
					// filtre yaparken esc ye basildiginda reset at
					add_event( $AH('filter_form'), "keyup", function(e){
						event_prevent_default( e ); 
						if( e.keyCode == 27 ){
							this_ref.reset();
							$AH('filter_form').reset();
						} else if( e.keyCode == 13 ){
							// submit
							filter_actions();
							
						}
					});
					// sadece otobusler.php de bunu koymadan enter a basildiginda prevent default yapabildi ???????*
					add_event( $AH('filter_form'), 'submit', function(ev){ event_prevent_default(ev)});

				}
				// butonlarla arama
				if( options.filter_inputs != undefined ){

					add_event( find_elem( options.filter_container, "#filter_uygula" ), "click", function(){
						filter_actions();
					});  
					add_event( find_elem( options.filter_container, "#filter_reset" ), "click", function(){
						this_ref.reset();
						$AH('filter_form').reset();
					});
					
				}
		    },
		    this.filter = function( data ){
		   		this.filter_data = data;
		   		this.filtered = true;
		   		this.page = 1;
		   		this.record_count = 0;
		        this.page_count = 0;
		        this.data_filtered = [];
		   		// orjinal data harici filtreli yeni liste olusturuyoruz
		   		for( var i = 0; i < this.data.length; i++ ){
		   			var item = this.data[i], off_filter = false;
		   			for( var filter in this.filter_data ){
		        		if( ( item[filter] != this.filter_data[filter] ) && ( item[filter] != this.filter_data[filter].toUpperCase() )  ){
		        			// bir tane bile uymadiysa bu item i geç
		        			off_filter = true;
		        			continue;
		        		} 
		        	}
		        	if( off_filter ) continue;
		        	this.record_count++;
		        	this.data_filtered.push( this.data[i] );
		   		}
		   		this.init();
		    },
		    this.reset = function(){
		   		this.filter_data = [];
		   		this.filtered = false;
		   		this.page = 1;
		   		this.data_filtered = [];
		   		this.init();
		    },
		    this.find_item_index = function( data, key, val ){
		        for( var i = 0; i < data.length; i++ ){
		            if( data[i][key] == val ){
		                return i;
		            }
		        }
		    },
		    this.find_item = function( key, val ){
		        var item_index = this.find_item_index( key, val );
		        if( this.active_data[item_index] != undefined ) return this.active_data[item_index];
		        return false;
		    },
		    this.update_item = function( key, val, data ){
		    	if( this.filtered ) overwrite( this.data[this.find_item_index(this.data, key, val)], data );   
		        overwrite( this.active_data[this.find_item_index(this.active_data, key, val)], data );   
		    },
		    this.delete_item = function( key, val ){
		    	// filtreli halde silme islemi yaparsak, orj veriden de siliyoruz itemi
		    	if( this.filtered ) this.data.splice( this.find_item_index(this.data, key, val), 1  );
		        this.active_data.splice( this.find_item_index(this.active_data, key, val), 1 );
		    }
		};


	