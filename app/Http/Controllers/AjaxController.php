<?php
namespace App\Http\Controllers;

use Fanky\Admin\Models\Feedback;
use Mail;
use Settings;
use Validator;

class AjaxController extends Controller
{
    private $fromMail = 'info@global-tkk.ru';
    private $fromName = 'Global Logistics';

    public function postCalc() {
        $data = request()->only(['name', 'phone', 'email', 'text']);

        $valid = Validator::make($data, [
            'phone' => 'required',
        ], [
            'phone.required' => 'Не заполнено поле телефон',
        ]);

        if ($valid->fails()) {
            return ['errors' => $valid->messages()];
        } else {
            $feedback_data = [
                'type' => 1,
                'data' => $data
            ];

            $feedback = Feedback::create($feedback_data);
            Mail::send('mail.feedback', ['feedback' => $feedback],
                function ($message) use ($feedback) {
                $title = $feedback->id . ' | Расчёт стоимости | ' . $this->fromName;
                $message->from($this->fromMail, $this->fromName)
                    ->to(Settings::get('feedback_email'))
                    ->subject($title);
            });

            return ['success' => true];
        }
    }
}
