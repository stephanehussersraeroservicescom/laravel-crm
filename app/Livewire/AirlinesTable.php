<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Airline;
#[Title('Airlines')]

class AirlinesTable extends Component
{
    public $name = '';
    public $region = '';
    public $account_executive = '';
    public $editing = false;
    public $editId = null;

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'account_executive' => 'nullable|string|max:255',
        ]);

        if ($this->editing && $this->editId) {
            $airline = Airline::find($this->editId);
            if ($airline) {
                $airline->update([
                    'name' => $this->name,
                    'region' => $this->region,
                    'account_executive' => $this->account_executive,
                ]);
            }
        } else {
            Airline::create([
                'name' => $this->name,
                'region' => $this->region,
                'account_executive' => $this->account_executive,
            ]);
        }

        $this->resetFields();
    }

    public function edit($id)
    {
        $airline = Airline::findOrFail($id);
        $this->name = $airline->name;
        $this->region = $airline->region;
        $this->account_executive = $airline->account_executive;
        $this->editId = $id;
        $this->editing = true;
    }

    public function cancelEdit()
    {
        $this->resetFields();
    }

    public function delete($id)
    {
        Airline::findOrFail($id)->delete();
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->name = '';
        $this->region = '';
        $this->account_executive = '';
        $this->editing = false;
        $this->editId = null;
    }

    public function render()
    {
        return view('livewire.airlines-table', [
            'airlines' => Airline::orderBy('name')->get()
        ])->layout('layouts.app');
    }
}
