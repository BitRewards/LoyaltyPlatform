<?php

namespace Helper;

use App\Administrator;
use App\Models\Partner;
use Carbon\Carbon;
use Codeception\Module\Laravel5;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laravel\Nova\Metrics\TrendResult;

class LaravelExtra extends \Codeception\Module
{
    protected function getLaravelModule(): Laravel5
    {
        return $this->getModule('Laravel5');
    }

    /**
     * Create model with states.
     *
     * @param string          $model
     * @param string|string[] $states
     * @param array           $attributes
     * @param string          $name
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function haveWithStates(string $model, $states, $attributes = [], string $name = 'default'): Model
    {
        try {
            $result = factory($model, $name)->states($states)->create($attributes);

            if ($result instanceof Collection) {
                $result = $result[0];
            }

            return $result;
        } catch (\Exception $e) {
            $this->fail("Could not create model: \n\n".get_class($e)."\n\n".$e->getMessage());
        }
    }

    /**
     * @param array       $attributes
     * @param string|null $driver
     *
     * @return \App\Models\Partner
     */
    public function amLoggedAsPartner(array $attributes = [], ?string $driver = null): Partner
    {
        /** @var Partner $partner */
        $partner = $this->getLaravelModule()->have(Partner::class, $attributes);
        $administrator = $this->getLaravelModule()->have(
            Administrator::class,
            [
                'partner_id' => $partner->id,
                'role' => Administrator::ROLE_PARTNER,
                'is_main' => true,
            ]
        );

        $this->getLaravelModule()->amLoggedAs($administrator, $driver);

        return $partner;
    }

    public function debugDBState()
    {
        \DB::commit();

        die('All data committed! Test stopped!');
    }

    /**
     * @param \Laravel\Nova\Metrics\TrendResult $trendResult
     * @param string|\DateTime                  $trendDate
     * @param mixed                             $value
     */
    public function seeTrendEqual(TrendResult $trendResult, $trendDate, $value)
    {
        $date = Carbon::make($trendDate)->format('Y-m-d');

        if (!isset($trendResult->trend[$date])) {
            $this->fail("Trend not found for ${date} date");
        }

        $this->assertEquals($value, $trendResult->trend[$date]);
    }

    /**
     * @param \Laravel\Nova\Metrics\TrendResult $trendResult
     * @param mixed                             $value
     */
    public function seeTrendValueIs(TrendResult $trendResult, $value)
    {
        $this->assertEquals($trendResult->value, $value);
    }
}
