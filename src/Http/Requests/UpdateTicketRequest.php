<?php

namespace Wiensa\SupportTicket\Http\Requests;

use Wiensa\SupportTicket\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');
        
        return $ticket instanceof Ticket && 
               $this->user() && 
               $this->user()->can('update', $ticket);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'category' => ['nullable', 'exists:support_ticket_categories,id'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'status' => ['required', 'in:open,pending,resolved,closed'],
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
            'subject.required' => __('supportticket::validation.subject_required'),
            'subject.max' => __('supportticket::validation.subject_max'),
            'message.required' => __('supportticket::validation.message_required'),
            'priority.required' => __('supportticket::validation.priority_required'),
            'priority.in' => __('supportticket::validation.priority_in'),
            'status.required' => __('supportticket::validation.status_required'),
            'status.in' => __('supportticket::validation.status_in'),
        ];
    }
} 