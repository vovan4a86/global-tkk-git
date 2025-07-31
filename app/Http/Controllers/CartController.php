<?php namespace App\Http\Controllers;

use Artesaos\SEOTools\Facades\SEOMeta;
use Fanky\Admin\Models\Product;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request as Request;
use Cart;
use Fanky\Admin\Models\Order as Order;

class CartController extends Controller {

	public function getIndex() {
//        Cart::purge();
		$items = Cart::all();

        $bread[] = [
            'url'  => '/cart',
            'name' => 'Корзина'
        ];

        SEOMeta::setTitle('Корзина');

        return view('cart.index', [
			'items' => $items,
            'bread' => $bread,
            'h1' => 'Корзина'
		]);
	}

	public function postIndex(Request $request) {
		$result = ['error' => false, 'msg' => ''];
		$messages = array(
			'email.required'           => 'Не указан ваш e-mail адрес!',
			'email.email'              => 'Не корректный e-mail адрес!',
			'name.required'            => 'Не заполнено поле Имя',
			'phone.required'           => 'Не заполнено поле Телефон',
			'delivery_method.required' => 'Не выбран способ доставки',
			'payment_method.required'  => 'Не выбран способ оплаты',
		);
		$this->validate($request, [
//			'name'            => 'required',
//			'email'           => 'required|email',
//			'phone'           => 'required',
//			'delivery_method' => 'required',
//			'payment_method'  => 'required',
		], $messages);
		$data = $request->only(['delivery_method', 'payment_method', 'name', 'phone', 'email']);
		/** @var Order $order */
		$order = Order::create($data);
		$items = Cart::all();
		$summ = 0;
		$all_count = 0;
		foreach ($items as $item) {
			$order->products()->attach($item['id'], [
				'count' => $item['count'],
				'price' => $item['price']
			]);
			$summ += $item['count'] * Product::fullPrice($item['price']);
			$all_count += $item['count'];
		}
		$order->update(['summ' => $summ]);

//		Mailer::sendNotification('mail.order',[
//			'order' => $order,
//			'items'	=> $items,
//			'all_count'	=> $all_count,
//			'all_summ'	=> $summ
//		], function($message){
//			$to = Settings::get('order_email');
//
//			/** @var Message $message */
//			$message->from('info@allant.ru', 'allant.ru - уведомления')
//				->to($to)
//				->subject('allant.ru - Новый заказ');
//		});

		Cart::purge();

		return json_encode($result);
	}

	public function getCreateOrder() {
        $items = Cart::all();

        $delivery = DeliveryItem::all();

        return view('cart.create_order', [
            'items' => $items,
            'delivery' => $delivery,
            'sum' => Cart::sum(),
            'total_weight' => Cart::total_weight(),
            'headerIsWhite' => true,
        ]);
    }

	protected function formatValidationErrors(Validator $validator): array
    {
		$msg = $validator->errors()->all('<p>:message</p>');

		return ['error' => true, 'msg' => implode('', $msg)];
	}

    public function showSuccess($id) {
//        $id = $request->get('id');

        return view('cart.success', [
            'id' => $id,
        ]);
    }
}
