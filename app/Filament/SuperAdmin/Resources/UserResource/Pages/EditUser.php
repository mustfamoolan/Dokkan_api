<?php

namespace App\Filament\SuperAdmin\Resources\UserResource\Pages;

use App\Filament\SuperAdmin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('renew_subscription')
                ->label('تجديد الاشتراك')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $user = $this->record;
                    $subscription = $user->subscription;
                    if ($subscription && $subscription->plan) {
                        $duration = $subscription->plan->duration_days ?? 30;
                        $subscription->update([
                            'end_date' => $subscription->end_date->max(now())->addDays($duration),
                            'is_active' => true,
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('تم تجديد الاشتراك بنجاح')
                            ->success()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('لا يوجد اشتراك فعال أو باقة محددة')
                            ->warning()
                            ->send();
                    }
                }),

            Actions\Action::make('change_plan')
                ->label('تغيير الباقة')
                ->icon('heroicon-o-ticket')
                ->form([
                    \Filament\Forms\Components\Select::make('subscription_plan_id')
                        ->label('الباقة الجديدة')
                        ->options(\App\Models\SubscriptionPlan::pluck('name', 'id'))
                        ->required(),
                ])
                ->action(function (array $data) {
                    $user = $this->record;
                    $plan = \App\Models\SubscriptionPlan::find($data['subscription_plan_id']);

                    if ($plan) {
                        $user->subscription()->updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'subscription_plan_id' => $plan->id,
                                'plan_name' => $plan->name, // Fallback
                                'start_date' => now(),
                                'end_date' => now()->addDays($plan->duration_days),
                                'is_active' => true,
                                'price' => $plan->price, // Assuming subscription table has price, or we add it. 
                                // schema has payment_status, maybe we don't store price on subscription directly but on plan.
                            ]
                        );

                        \Filament\Notifications\Notification::make()
                            ->title("تم تغيير الباقة إلى {$plan->name}")
                            ->success()
                            ->send();
                    }
                }),

            Actions\DeleteAction::make(),
        ];
    }
}
