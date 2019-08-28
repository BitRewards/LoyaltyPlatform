<?php

namespace App\Http\Controllers\Api\Search;

use HUser;
use App\Models\Code;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;
use Illuminate\Database\Eloquent\Builder;

class UsersController extends Controller
{
    /**
     * Find users by email, card or phone.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Str::lower($request->input('query'));
        $partner = $request->user()->partner;

        $users = User::where('partner_id', $partner->id)
            ->where(function (Builder $builder) use ($query, $partner) {
                $builder->where(DB::raw('LOWER(name)'), 'LIKE', '%'.mb_strtolower($query).'%');

                if (Str::contains($query, '@')) {
                    $builder->orWhere(DB::raw('email'), HUser::normalizeEmail($query));
                }

                if (strlen($phone = HUser::normalizePhone($query, $partner->default_country)) > 6) {
                    $builder->orWhere(DB::raw('phone'), 'LIKE', "%{$phone}%");
                }

                $builder->orWhere('referral_promo_code', trim($query));
            })
            ->get();

        if ($digitalQuery = Code::normalizeToken($query)) {
            Code::where('partner_id', $partner->id)
                ->where('token', $digitalQuery)
                ->whereNotNull('user_id')
                ->with('user.codes')
                ->get()
                ->each(function ($code) use ($users) {
                    $users->push($code->user);
                });
        }

        return response()->json(
            fractal($users, new UserTransformer())
        );
    }
}
