<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;

class Settings extends Component
{
    public $tab = 'general_settings';

    protected $queryString = [
        'tab' => ['keep' => true]
    ];

    //General settings form properties
    public $site_title, $site_email, $site_description, $site_phone, $site_meta_keywords, $site_meta_description, $site_logo, $site_favicon, $site_copyright;


    public function selectTab($tab)
    {
        $this->tab = $tab;
    }
    public function mount(Request $request)
    {
        $this->tab = $request->query('tab', $this->tab);

        //Populate General Settings
        $settings = GeneralSetting::take(1)->first();
        if( !is_null($settings)){
            $this->site_title = $settings->site_title;
            $this->site_email = $settings->site_email;
            $this->site_description = $settings->site_description;
            $this->site_phone = $settings->site_phone;
            $this->site_meta_keywords = $settings->site_meta_keywords;
            $this->site_meta_description = $settings->site_meta_description;
            $this->site_logo = $settings->site_logo;
            $this->site_favicon = $settings->site_favicon;
            $this->site_copyright = $settings->site_copyright;
        }
    }

    public function updateSiteInfo()
    {
        $this->validate([
            'site_title' => 'required',
            'site_email' => 'required',
        ]);
        $settings = GeneralSetting::take(1)->first();
        $data = array(
            'site_title' => $this->site_title,
            'site_email' => $this->site_email,
            'site_description' => $this->site_description,
            'site_phone' => $this->site_phone,
            'site_meta_keywords' => $this->site_meta_keywords,
            'site_meta_description' => $this->site_meta_description,
            'site_logo' => $this->site_logo,
            'site_favicon' => $this->site_favicon,
            'site_copyright' => $this->site_copyright,
        );
        if(!is_null($settings)){
            $query = $settings->update($data);
        } else {
            $query = GeneralSetting::insert($data);
        }

        if($query){
            $this->dispatch('showToastr', ['type'=> 'success', 'message' => 'General Setting Updated Successfully']);
        } else {
            $this->dispatch('showToastr', ['type'=> 'error', 'message' => 'General Setting Not Updated']);
        }
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }
}
