<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LandingPageSetting;
use Illuminate\Http\Request;

class LandingPageSettingsController extends Controller
{
    public function show()
    {
        $settings = LandingPageSetting::first();
        if (!$settings) {
            $settings = $this->seedDefaultSettings();
        }
        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $settings = LandingPageSetting::first();
        if (!$settings) {
            $settings = $this->seedDefaultSettings();
        }

        $validated = $request->validate([
            'hero_title_en' => 'nullable|string',
            'hero_title_ar' => 'nullable|string',
            'hero_subtitle_en' => 'nullable|string',
            'hero_subtitle_ar' => 'nullable|string',
            'features' => 'nullable|array',
            'steps' => 'nullable|array',
            'cta_title_en' => 'nullable|string',
            'cta_title_ar' => 'nullable|string',
        ]);

        $settings->update($validated);

        return response()->json($settings);
    }

    private function seedDefaultSettings()
    {
        return LandingPageSetting::create([
            'hero_title_en' => 'Clothing Manufacturing Platform',
            'hero_title_ar' => 'منصة تصنيع الملابس الأولى',
            'hero_subtitle_en' => 'Manage clothing production between brands and factories easily. The ultimate digital pattern & tech pack platform.',
            'hero_subtitle_ar' => 'أدر عملية تصنيع الملابس بين العلامات التجارية والمصانع بكل سهولة. منصة الملفات الفنية والقوالب الرقمية المتكاملة.',
            'features' => [
                [
                    'icon' => 'create_order',
                    'title_en' => 'Create Manufacturing Orders',
                    'title_ar' => 'إنشاء طلبات تصنيع',
                    'desc_en' => 'Upload designs and measurements with detailed tech packs for precise production.',
                    'desc_ar' => 'ارفع تصميماتك وقياساتك مع ملفات فنية مفصلة لضمان دقة الإنتاج.'
                ],
                [
                    'icon' => 'fabric',
                    'title_en' => 'Fabric and Color Selection',
                    'title_ar' => 'اختيار الأقمشة والألوان',
                    'desc_en' => 'Choose materials and colors accurately using Pantone codes and texture details.',
                    'desc_ar' => 'اختر الأنماط والألوان بدقة باستعمال أكواد بانتون وتفاصيل الخامات.'
                ],
                [
                    'icon' => 'track',
                    'title_en' => 'Track Order Status',
                    'title_ar' => 'تتبع حالة الطلب',
                    'desc_en' => 'Monitor production progress in real-time from pattern making to final delivery.',
                    'desc_ar' => 'راقب تقدم الإنتاج في الوقت الفعلي من مرحلة القالب حتى التسليم النهائي.'
                ],
                [
                    'icon' => 'chat',
                    'title_en' => 'Direct Factory Communication',
                    'title_ar' => 'تواصل مباشر مع المصنع',
                    'desc_en' => 'Chat directly with manufacturers to clarify details and ensure quality standards.',
                    'desc_ar' => 'تواصل مباشرة مع المصنعين لتوضيح التفاصيل وضمان معايير الجودة.'
                ],
            ],
            'steps' => [
                [
                    'title_en' => 'Create your brand account',
                    'title_ar' => 'أنشئ حساباً لعلامتك التجارية',
                    'desc_en' => 'Sign up and set up your brand profile in minutes.',
                    'desc_ar' => 'سجل الآن وأنشئ بروفايل علامتك التجارية في دقائق.'
                ],
                [
                    'title_en' => 'Submit manufacturing orders',
                    'title_ar' => 'أرسل طلبات التصنيع',
                    'desc_en' => 'Fill in technical specs and upload your beautiful designs.',
                    'desc_ar' => 'أدخل المواصفات الفنية وارفع تصميماتك الرائعة.'
                ],
                [
                    'title_en' => 'Communicate & Track',
                    'title_ar' => 'تواصل وتتبع',
                    'desc_en' => 'Chat with factories and watch your design come to life.',
                    'desc_ar' => 'تواصل مع المصانع وشاهد تصميماتك تتحول لواقع ملموس.'
                ],
            ],
            'cta_title_en' => 'Start managing your clothing production today.',
            'cta_title_ar' => 'ابدأ في إدارة إنتاج ملابسك اليوم.',
        ]);
    }
}
