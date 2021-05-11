<?php

namespace App\Repository;

use App\Models\Dealers;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Vehicles;

class DealerAndVehicleRepository
{

    const AVAILABLE =  'Active';
    const SOLD = 'Sold';
    const RESERVED = 'Processing';

    /**
     *
     * @param array $dealerFileContent
     * @param array $carFileContent
     */
    public  function process(array $dealerFileContent, array $carFileContent)
    {
        return $this->create($dealerFileContent, $carFileContent);
    }

    /**
     *$dealerErrors[] = Checks::makeException($exception);
     * @param array $dealerFileContent
     * @param array $carFileContent
     */
    private function create(array $dealerFileContent, array $carFileContent)
    {

        try {

            $dealerCollection = $this->collectGroupBy($dealerFileContent, 'id');
            foreach ($dealerCollection as $dealerKey => $dealer) {
                $dealer = $this->fillDealer($dealer);
                if ($dealer->save()) {
                    DB::commit();
                    $cars = $this->searchByDealerId($dealerKey, $carFileContent);
                    foreach ($cars as  $car) {

                        $makeVehicle = $this->fillVehicle($car);
                        $dealer->vehicles()->save($makeVehicle);
                        DB::commit();
                    }
                }
            }
            return true;
        } catch (\Exception $exception) {
            return false;
        } catch (ValidationException $validationException) {
            throw $validationException;
        } finally {

            // This still gets called even if we return within the try, which is default behavior across a lot OOP languages (C++, Java, PHP, etc..)
            DB::rollback();
        }
    }

    /**
     *
     * @param array $dealerData
     * @return Dealers
     */
    public function fillDealer(array $dealerData): Dealers
    {
        $dealer = new Dealers();
        $dealer->unique_id = Arr::get($dealerData, 'id');
        $dealer->fill($dealerData);
        return $dealer;
    }

    /**
     *
     * @param array $vehicleData
     * @return Vehicles
     */
    public function fillVehicle(array $vehicleData): Vehicles
    {
        $vehicle = new Vehicles();
        $vehicle->unique_id = Arr::get($vehicleData, 'id');
        $vehicle->fill($vehicleData);
        return $vehicle;
    }

    /**
     *
     * @param array $array
     * @param string $key
     * @return Collection
     */
    public function collectGroupBy(array $array, string $key): Collection
    {
        return collect($array)
            ->groupBy($key)
            ->map(function ($item) {
                return array_merge(...$item->toArray());
            });
    }

    /**
     *
     * @param string $dealerId
     * @param array $carsArray
     * @return array
     */
    public function searchByDealerId(string $dealerId, array $carsArray): array
    {
        $cars = [];
        foreach ($carsArray as $car) {
            if ($car['vehicle_dealer_id'] === $dealerId) {
                $cars[] = $car;
            }
        }
        return $cars;
    }

    /**
     *
     * @return mixed
     */
    public function getDealerSummary()
    {
        return Dealers::query()
            ->select('name')
            ->withCount(['vehicles as available_count' => function ($query) {
                $query->where('status', static::AVAILABLE);
            }])
            ->withCount(['vehicles as sold_count' => function ($query) {
                $query->where('status', static::SOLD);
            }])
            ->withCount(['vehicles as reserved_count' => function ($query) {
                $query->where('status', static::RESERVED);
            }])
            ->withCount('vehicles')
            ->get();
    }
}
