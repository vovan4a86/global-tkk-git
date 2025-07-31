<?php
namespace App\Http\Controllers;

use DB;
use Fanky\Admin\Models\Catalog;
use Fanky\Admin\Models\Char;
use Fanky\Admin\Models\City;
use Fanky\Admin\Models\Feedback;
use Fanky\Admin\Models\Order as Order;
use Fanky\Admin\Models\Page;
use Fanky\Admin\Models\Product;
use Fanky\Admin\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Mail;
use Settings;
use Cart;
use SiteHelper;
use Validator;

class AjaxController extends Controller
{
    private $fromMail = 'info@mansardnie-okna96.ru';
    private $fromName = 'Мир мансардных окон';

    //РАБОТА С КОРЗИНОЙ
    public function postAddToCart(): array
    {
        $id = request()->get('id');
        $count = request()->get('count');

        /** @var Product $product */
        $product = Product::find($id);
        if ($product) {
            $product_item['id'] = $product->id;
            $product_item['name'] = $product->name;
            $product_item['price'] = $product->price;
            $product_item['measure'] = $product->measure ?: 'шт';
            $product_item['count'] = $count;
            $product_item['url'] = $product->getUrl($product->catalog_id);

            if ($product->single_image) {
                $product_item['image'] = $product->single_image->thumb(1);
            } else {
                $product_item['image'] = $product->catalog->thumb(1);
            }

            Cart::add($product_item);
        }
        $header_cart = view('blocks.header_cart')->render();
        $mobile_cart = view('blocks.h_mobile_cart')->render();
        $btn = view('catalog.product_add_btn', ['in_cart' => true, 'product' => $product])->render();

        return [
            'success' => true,
            'header_cart' => $header_cart,
            'mobile_cart' => $mobile_cart,
            'btn' => $btn
        ];
    }

    public function postRemoveFromCart(): array
    {
        $id = request()->get('id');
        Cart::remove($id);
        $cart_total = view('cart.cart_total')->render();
        $header_cart = view('blocks.header_cart')->render();

        return [
            'success' => true,
            'cart_total' => $cart_total,
            'header_cart' => $header_cart,
            'cart_count' => Cart::count()
        ];
    }

    public function postUpdateToCart(): array
    {
        $id = request()->get('id');
        $count = request()->get('count');

        if(!$id || !$count) return ['success' => false];

        Cart::updateCount($id, $count);
        $cart_total = view('cart.cart_total')->render();

        return [
            'success' => true,
            'cart_total' => $cart_total
        ];
    }

    public function postEditCartProduct(Request $request): array
    {
        $id = $request->get('id');
        $count = $request->get('count', 1);
        /** @var Product $product */
        $product = Product::find($id);
        if ($product) {
            $product_item['image'] = $product->showAnyImage();
            $product_item = $product->toArray();
            $product_item['count_per_tonn'] = $count;
            $product_item['url'] = $product->url;

            Cart::add($product_item);
        }

        $popup = view('blocks.cart_popup', $product_item)->render();

        return ['cart_popup' => $popup];
    }

    public function postPurgeCart(): array
    {
        Cart::purge();
        $footer_total = view('cart.footer_total')->render();
        $header_cart = view('blocks.header_cart')->render();
        return [
            'success' => true,
            'footer_total' => $footer_total,
            'header_cart' => $header_cart
        ];
    }

    //бесплатный расчет +
    public function postFreeCalc() {
        $data = request()->only(['name', 'phone', 'text', 'montage', 'delivery']);
        $file = request()->file('file');

        $data['montage'] = isset($data['montage']) ? 'Да' : 'Нет';
        $data['delivery'] = isset($data['delivery']) ? 'Да' : 'Нет';

        $valid = Validator::make($data, [
            'name' => 'required',
            'phone' => 'required',
            'text' => 'required',
        ], [
            'name.required' => 'Не заполнено поле имя',
            'phone.required' => 'Не заполнено поле телефон',
            'text.required' => 'Не заполнено поле сообщение',
        ]);

        if ($valid->fails()) {
            return ['errors' => $valid->messages()];
        } else {
            if ($file) {
                $file_name = md5(uniqid(rand(), true)) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path(Feedback::UPLOAD_URL), $file_name);
                $data['file'] = '<a target="_blanc" href=\'' . Feedback::UPLOAD_URL . $file_name . '\'>' . $file_name . '</a>';
            }

            $feedback_data = [
                'type' => 1,
                'data' => $data
            ];

            $feedback = Feedback::create($feedback_data);
            Mail::send('mail.feedback', ['feedback' => $feedback], function ($message) use ($feedback) {
                $title = $feedback->id . ' | Бесплатный расчет | ' . $this->fromName;
                $message->from($this->fromMail, $this->fromName)
                    ->to(Settings::get('feedback_email'))
                    ->subject($title);
            });

            return ['success' => true];
        }
    }

    //обратный звонок +
    public function postCallback() {
        $data = request()->only(['name', 'phone']);

        $valid = Validator::make($data, [
            'name' => 'required',
            'phone' => 'required',
        ], [
            'name.required' => 'Не заполнено поле имя',
            'phone.required' => 'Не заполнено поле телефон',
        ]);

        if ($valid->fails()) {
            return ['errors' => $valid->messages()];
        } else {
            $feedback_data = [
                'type' => 2,
                'data' => $data
            ];

            $feedback = Feedback::create($feedback_data);
            Mail::send('mail.feedback', ['feedback' => $feedback],
                function ($message) use ($feedback) {
                $title = $feedback->id . ' | Обратный звонок | ' . $this->fromName;
                $message->from($this->fromMail, $this->fromName)
                    ->to(Settings::get('feedback_email'))
                    ->subject($title);
            });

            return ['success' => true];
        }
    }

    //вопрос +
    public function postQuestion() {
        $data = request()->only(['name', 'phone', 'text']);

        $valid = Validator::make($data, [
            'name' => 'required',
            'phone' => 'required',
            'text' => 'required',
        ], [
            'name.required' => 'Не заполнено поле имя',
            'phone.required' => 'Не заполнено поле телефон',
            'text.required' => 'Не заполнено поле сообщение',
        ]);

        if ($valid->fails()) {
            return ['errors' => $valid->messages()];
        } else {
            $feedback_data = [
                'type' => 3,
                'data' => $data
            ];

            $feedback = Feedback::create($feedback_data);
            Mail::send('mail.feedback', ['feedback' => $feedback],
                function ($message) use ($feedback) {
                $title = $feedback->id . ' | Вопрос | ' . $this->fromName;
                $message->from($this->fromMail, $this->fromName)
                    ->to(Settings::get('feedback_email'))
                    ->subject($title);
            });

            return ['success' => true];
        }
    }

    //заказать замерщика +
    public function postInvite() {
        $data = request()->only(['name', 'phone', 'address']);

        $valid = Validator::make($data, [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ], [
            'name.required' => 'Не заполнено поле имя',
            'phone.required' => 'Не заполнено поле телефон',
            'address.required' => 'Не заполнено поле адрес',
        ]);

        if ($valid->fails()) {
            return ['errors' => $valid->messages()];
        } else {
            $feedback_data = [
                'type' => 4,
                'data' => $data
            ];

            $feedback = Feedback::create($feedback_data);
            Mail::send('mail.feedback', ['feedback' => $feedback],
                function ($message) use ($feedback) {
                $title = $feedback->id . ' | Вызов замерщика | ' . $this->fromName;
                $message->from($this->fromMail, $this->fromName)
                    ->to(Settings::get('feedback_email'))
                    ->subject($title);
            });

            return ['success' => true];
        }
    }

    //заявка на расчёт +
    public function postRequestCalc() {
        $data = request()->only(['name', 'phone', 'text', 'product']);

        $valid = Validator::make($data, [
            'name' => 'required',
            'phone' => 'required',
            'text' => 'required',
            'product' => 'required',
        ], [
            'name.required' => 'Не заполнено поле имя',
            'phone.required' => 'Не заполнено поле телефон',
            'text.required' => 'Не заполнено поле комментарий',
            'product.required' => 'Не заполнено поле товар',
        ]);

        if ($valid->fails()) {
            return ['errors' => $valid->messages()];
        } else {
            $feedback_data = [
                'type' => 5,
                'data' => $data
            ];

            $feedback = Feedback::create($feedback_data);
            Mail::send('mail.feedback', ['feedback' => $feedback],
                function ($message) use ($feedback) {
                $title = $feedback->id . ' | Заявка на расчёт | ' . $this->fromName;
                $message->from($this->fromMail, $this->fromName)
                    ->to(Settings::get('feedback_email'))
                    ->subject($title);
            });

            return ['success' => true];
        }
    }

    //поиск
    public function search(Request $request)
    {
        $data = $request->only(['search']);

        $items = null;

        $page = Page::getByPath(['search']);
        $bread = $page->getBread();

        return [
            'success' => true,
            'redirect' => url('/search', [
                'bread' => $bread,
                'items' => $items,
                'data' => $data,
            ])
        ];

//        return view('search.index', [
//            'bread' => $bread,
//            'items' => $items,
//            'data' => $data,
//        ]);

    }

    //ОФОРМЛЕНИЕ ЗАКАЗА
    public function postOrder(Request $request)
    {
        $data = $request->only([
            'name',
            'phone',
            'email',
        ]);

        $messages = array(
            'name.required' => 'Не заполнено поле Фамилия',
            'phone.required' => 'Не заполнено поле Телефон',
            'email.required' => 'Не выбран способ Email',
        );

        $valid = Validator::make($data, [
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
        ], $messages);
        if ($valid->fails()) {
            return ['errors' => $valid->messages()];
        }

        $data['summ'] = Cart::sum();

        $order = Order::create($data);
        $items = Cart::all();

        foreach ($items as $item) {
            $itemPrice = $item['price'] * $item['count'];
            $order->products()->attach($item['id'], [
                'count' => $item['count'],
                'price' => $itemPrice,
            ]);
        }

        $order_items = $order->products;
        $all_count = 0;
        $all_summ = 0;
        foreach ($order_items as $item) {
            $all_summ += $item->pivot->price;
            $all_count += $item->pivot->count;
        }

        Mail::send('mail.new_order_table', [
            'order' => $order,
            'items' => $order_items,
            'all_count' => $all_count,
            'all_summ' => $all_summ,
        ], function ($message) use ($order) {
            $title = $order->id . ' | Новый заказ | ' . $this->fromName;
            $message->from($this->fromMail, $this->fromName)
                ->to(Settings::get('feedback_email'))
                ->subject($title);
        });

        Cart::purge();

        return ['success' => true];
    }

    //РАБОТА С ГОРОДАМИ
    public function postSetCity(Request $request)
    {
        $city_id = $request->get('city_id');
        $city = City::find($city_id);
        if ($city) {
            $result = [
                'success' => true,
            ];
            session(['city_alias' => $city->alias]);

            return response(json_encode($result))->withCookie(cookie('city_id', $city->id));
        } elseif ($city_id == 0) {
            $result = [
                'success' => true,
            ];
            session(['city_alias' => '']);

            return response(json_encode($result))->withCookie(cookie('city_id', 0));
        }

        return ['success' => false, 'msg' => 'Город не найден'];
    }

    public function postGetCorrectRegionLink(Request $request)
    {
        $city_id = $request->get('city_id');
        $city = City::find($city_id);
        $cur_url = $request->get('cur_url');
        $url = $cur_url;
        $excludeRegionAlias = true;

        $cities_aliases = City::pluck('alias')->all();

        $path = explode('/', $cur_url);
        foreach ($cities_aliases as $alias) {
            if (in_array($alias, $path)) {
                $excludeRegionAlias = false;
                break;
            }
        }

        if ($cur_url != '/' && !$excludeRegionAlias) {
            $path = explode('/', $cur_url);
            $cities = City::pluck('alias')->all();
            /* проверяем - региональная ссылка или федеральная */
            if (in_array($path[3], $cities)) {
                if ($city) {
                    $path[3] = $city->alias;
                } else {
                    array_splice($path, 3,1);
                }
            } else {
                if ($city) {
                    array_splice($path, 3, 0, $city->alias);
                }
            }

            $url = implode('/', $path);
        }

        session(['city_alias' => $city ? $city->alias : null]);

        $result = ['success' => true, 'redirect' => url($url)];

        return response(json_encode($result))->withCookie(cookie('city_id', $city ? $city->id : 0));
    }

    public function showCitiesPopup()
    {
        $cities = Cache::get('cities', collect());
        if (!count($cities)) {
            $cities = City::query()->orderBy('name')
                ->get(['id', 'alias', 'name', DB::raw('LEFT(name,1) as letter')]);
            Cache::add('cities', $cities, now()->addMinutes(60));
        }
        $citiesArr = Cache::get('cities_arr', []);
        if (!count($citiesArr)) {
            if (count($cities)) {
                foreach ($cities as $city) {
                    $citiesArr[$city->letter][] = $city; //Группировка по первой букве
                }
            }
            Cache::add('cities_arr', $citiesArr, now()->addMinutes(60));
        }

        $curUrl = url()->previous() ?: '/';
        $curUrl = str_replace(url('/') . '/', '', $curUrl);

        $current_city = SiteHelper::getCurrentCity();

        return view(
            'blocks.popup_cities',
            [
                'cities' => $citiesArr,
                'curUrl' => $curUrl,
                'current_city' => $current_city
            ]
        );
    }

}
