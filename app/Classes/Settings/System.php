<?php

namespace App\Classes\Settings;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class System
{


    public function __construct()
    {
        return;
    }



    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "register-ip-check" => "string",
            "server-create-charge-first-hour" => "string",
            "credits-display-name" => "required|string",
            "allocation-limit" => "required|min:0|integer",
            "force-email-verification" => "string",
            "force-discord-verification" => "string",
            "initial-credits" => "required|min:0|integer",
            "initial-server-limit" => "required|min:0|integer",
            "credits-reward-amount-discord" => "required|min:0|integer",
            "credits-reward-amount-email" => "required|min:0|integer",
            "server-limit-discord" => "required|min:0|integer",
            "server-limit-email" => "required|min:0|integer",
            "server-limit-purchase" => "required|min:0|integer",
            "pterodactyl-api-key" => "required|string",
            "pterodactyl-url" => "required|string",

        ]);
        if ($validator->fails()) {
            return redirect(route('admin.settings.index') . '#system')->with('error', __('System settings have not been updated!'))->withErrors($validator)
                ->withInput();
        }

        // update Icons from request
        $this->updateIcons($request);


        $values = [
            "SETTINGS::SYSTEM:REGISTER_IP_CHECK" => "register-ip-check",
            "SETTINGS::SYSTEM:SERVER_CREATE_CHARGE_FIRST_HOUR" => "server-create-charge-first-hour",
            "SETTINGS::SYSTEM:CREDITS_DISPLAY_NAME" => "credits-display-name",
            "SETTINGS::SERVER:ALLOCATION_LIMIT" => "allocation-limit",
            "SETTINGS::USER:FORCE_DISCORD_VERIFICATION" => "force-discord-verification",
            "SETTINGS::USER:FORCE_EMAIL_VERIFICATION" => "force-email-verification",
            "SETTINGS::USER:INITIAL_CREDITS" => "initial-credits",
            "SETTINGS::USER:INITIAL_SERVER_LIMIT" => "initial-server-limit",
            "SETTINGS::USER:CREDITS_REWARD_AFTER_VERIFY_DISCORD" => "credits-reward-amount-discord",
            "SETTINGS::USER:CREDITS_REWARD_AFTER_VERIFY_EMAIL" => "credits-reward-amount-email",
            "SETTINGS::USER:SERVER_LIMIT_REWARD_AFTER_VERIFY_DISCORD" => "server-limit-discord",
            "SETTINGS::USER:SERVER_LIMIT_REWARD_AFTER_VERIFY_EMAIL" => "server-limit-email",
            "SETTINGS::USER:SERVER_LIMIT_AFTER_IRL_PURCHASE" => "server-limit-purchase",
            "SETTINGS::MISC:PHPMYADMIN:URL" => "phpmyadmin-url",
            "SETTINGS::SYSTEM:PTERODACTYL:URL" => "pterodactyl-url",
            "SETTINGS::SYSTEM:PTERODACTYL:TOKEN" => "pterodactyl-api-key",
            "SETTINGS::SYSTEM:ENABLE_LOGIN_LOGO" => "enable-login-logo",
        ];


        foreach ($values as $key => $value) {
            $param = $request->get($value);

            Settings::where('key', $key)->updateOrCreate(['key' => $key], ['value' => $param]);
            Cache::forget("setting" . ':' . $key);
        }
        return redirect(route('admin.settings.index') . '#system')->with('success', __('System settings updated!'));
    }

    private function updateIcons(Request $request)
    {
        $request->validate([
            'icon' => 'nullable|max:10000|mimes:jpg,png,jpeg',
            'logo' => 'nullable|max:10000|mimes:jpg,png,jpeg',
            'favicon' => 'nullable|max:10000|mimes:ico',
        ]);

        if ($request->hasFile('icon')) {
            $request->file('icon')->storeAs('public', 'icon.png');
        }
        if ($request->hasFile('logo')) {
            $request->file('logo')->storeAs('public', 'logo.png');
        }
        if ($request->hasFile('favicon')) {
            $request->file('favicon')->storeAs('public', 'favicon.ico');
        }
    }
}
