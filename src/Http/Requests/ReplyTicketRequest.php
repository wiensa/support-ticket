<?php

namespace Wiensa\SupportTicket\Http\Requests;

use Wiensa\SupportTicket\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class ReplyTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');
        
        return $ticket instanceof Ticket && 
               $this->user() && 
               $this->user()->can('reply', $ticket);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string'],
            'is_private' => ['sometimes', 'boolean'],
            'attachments.*' => ['sometimes', 'file', 'max:' . config('supportticket.attachments.max_size', 5120)],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'message.required' => __('supportticket::validation.reply_required'),
            'attachments.*.max' => __('supportticket::validation.attachment_max_size'),
        ];
    }
} 