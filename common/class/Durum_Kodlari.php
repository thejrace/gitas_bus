<?php

	class Durum_Kodlari {
		private $kodlar = array(
			'Sİ' => 'Sürücünün talimata uymaması sebebiyle iptal.',
			'SH' => 'Sürücünün talimata uymaması sebebiyle iptal.',
			'AR' => 'Arıza',
			'DL' => 'Dilekçeli',
			'FS' => 'Fazla Sefer',
			'GA' => 'Gözetim altı',
			'GG' => 'Garajdan geç gelme',
			'HD' => 'Hat değiştirme',
			'KZ' => 'Kaza',
			'OY' => 'Otobüs yokluğu',
			'PY' => 'Personel yokluğu',
			'TH' => 'Tahsis',
			'YA' => 'Yolcu azlığı',
			'YG' => 'Yolda gecikme',
			'RK' => 'Rotardan kurtarma amacıyla iptal',
			'KM' => 'Karayolları muayene sebebiyle iptal',
			'TO' => 'Toplumsal olaylar nedeniyle iptal',
			'CA' => 'Cihaz arızası',
			'SD' => 'Sürücü sağlık durumu sebebiyle iptal',
			'YS' => 'Yolcu sağlık durumu sebebiyle iptal',
			'AY' => 'Akaryakıt yokluğu sebebiyle iptal',
			'Cİ' => 'Cezadan iptal',
			'İH' => 'İhtiyaçtan hat değişimi sebebiyle iptal',
			'KG' => 'Kapalı güzergah sebebiyle iptal',
			'ST' => 'Saldırı - taciz sebebiyle iptal',
			'TK' => 'Temizlik kontrolü sebebiyle iptal',
			'AK' => 'Amir kararıyla iptal',
			'HM' => 'Hava muhalefeti sebebiyle iptal',
			'NM' => 'Zayi kararıyla iptal',
			'PM' => 'Personel müsaade sebebiyle iptal'
		);
		public function data( $kod ){
			return $this->kodlar[$kod];
		}
		public function get(){
			return $this->kodlar;
		}
	}