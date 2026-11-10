<?php
namespace App\Livewire;
use App\Models\Review; use Livewire\Component; use Livewire\WithPagination;
class UlasanPublik extends Component { use WithPagination; public $rating='';
 public function render(){ $q=Review::with('foto')->latest(); if($this->rating) $q->where('rating',$this->rating); return view('livewire.ulasan-publik',['reviews'=>$q->paginate(9),'rataRata'=>Review::avg('rating') ?: 0,'jumlah'=>Review::count()]); }
}
