<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseFormRequest;
use App\DTO\UsersBulkImport\ColumnMatching;
use App\Enums\UsersBulkImport\ImportMode;
use App\Models\User;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Validation\Rule;

class UsersBulkImportRequest extends BaseFormRequest
{
    public $columnMatching;

    public function authorize()
    {
        return User::ROLE_PARTNER == \Auth::user()->role;
    }

    public function rules()
    {
        return [
            'title' => 'required|string',
            'mode' => ['required', Rule::in([
                    ImportMode::CREATE_NEW_SKIP_EXISTING,
                    ImportMode::CREATE_NEW_UPDATE_EXISTING,
                    ImportMode::SKIP_NEW_UPDATE_EXISTING, ]
            )],
            'data' => 'string|nullable',
            'file' => 'file|nullable',
        ];
    }

    public function doExtraValidation(MessageBag $messageBag)
    {
        if (!$this->data && !$this->file) {
            $messageBag->add('data', _('Data or file should be specified'));

            return;
        }

        if ($this->data) {
            $row = \HStr::splitByNewLine($this->data)[0];
            $columns = \HStr::splitByTab($row);

            if (count($columns) < 2) {
                $messageBag->add('data', _('The tab character is not found! Please divide the columns with a tab!'));

                return;
            }

            $matching = new ColumnMatching($columns);

            if (!$matching->isValid()) {
                if (ColumnMatching::NOT_DETECTED == $matching->balance) {
                    $messageBag->add('data', _('The last column does not looks like the balance value'));
                } else {
                    $messageBag->add('data', _('In the first line of data, neither email nor phone was found'));
                }
            }
        }
    }
}
