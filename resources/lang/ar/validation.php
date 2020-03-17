<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */
    'accepted'             => 'يجب قبول :attribute.',
    'active_url'           => ':attribute لا يُمثّل رابطًا صحيحًا.',
    'after'                => 'يجب  :attribute أن يكون تاريخًا لاحقًا للتاريخ :date.',
    'after_or_equal'       => ':attribute يجب أن يكون تاريخاً لاحقاً أو مطابقاً للتاريخ :date.',
    'alpha'                => 'يجب أن لا يحتوي :attribute سوى على حروف.',
    'alpha_dash'           => 'يجب أن لا يحتوي :attribute سوى على حروف، أرقام ومطّات.',
    'alpha_num'            => 'يجب أن يحتوي :attribute على حروفٍ وأرقامٍ فقط.',
    'array'                => 'يجب أن يكون :attribute ًمصفوفة.',
    'before'               => 'يجب  :attribute أن يكون تاريخًا سابقًا للتاريخ :date.',
    'before_or_equal'      => ':attribute يجب أن يكون تاريخا سابقا أو مطابقا للتاريخ :date.',
    'between'              => [
        'numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.',
        'file'    => 'يجب أن يكون حجم الملف :attribute بين :min و :max كيلوبايت.',
        'string'  => 'يجب أن يكون عدد حروف النّص :attribute بين :min و :max.',
        'array'   => 'يجب أن يحتوي :attribute على عدد من العناصر بين :min و :max.',
    ],
    'boolean'              => 'يجب أن تكون قيمة :attribute إما 1 أو 0.',
    'confirmed'            => 'حقل التأكيد غير مُطابق للحقل :attribute.',
    'date'                 => ':attribute ليس تاريخًا صحيحًا.',
    'date_equals'          => 'يجب أن يكون :attribute مطابقاً للتاريخ :date.',
    'date_format'          => 'لا يتوافق :attribute مع الشكل :format.',
    'different'            => 'يجب أن يكون الحقلان :attribute و :other مُختلفين.',
    'digits'               => 'يجب أن يحتوي :attribute على :digits رقمًا/أرقام.',
    'digits_between'       => 'يجب أن يحتوي :attribute بين :min و :max رقمًا/أرقام .',
    'dimensions'           => 'الـ :attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct'             => 'للحقل :attribute قيمة مُكرّرة.',
    'email'                => 'يجب أن يكون :attribute عنوان بريد إلكتروني صحيح البُنية.',
    'exists'               => 'القيمة المحددة :attribute غير موجودة.',
    'file'                 => 'الـ :attribute يجب أن يكون ملفا.',
    'filled'               => ':attribute إجباري.',
    'gt'                   => [
        'numeric' => 'يجب أن تكون قيمة :attribute أكبر من :value.',
        'file'    => 'يجب أن يكون حجم الملف :attribute أكبر من :value كيلوبايت.',
        'string'  => 'يجب أن يكون طول النّص :attribute أكثر من :value حروفٍ/حرفًا.',
        'array'   => 'يجب أن يحتوي :attribute على أكثر من :value عناصر/عنصر.',
    ],
    'gte'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أكبر من :value.',
        'file'    => 'يجب أن يكون حجم الملف :attribute على الأقل :value كيلوبايت.',
        'string'  => 'يجب أن يكون طول النص :attribute على الأقل :value حروفٍ/حرفًا.',
        'array'   => 'يجب أن يحتوي :attribute على الأقل على :value عُنصرًا/عناصر.',
    ],
    'image'                => 'يجب أن يكون :attribute صورةً.',
    'in'                   => ':attribute غير موجود.',
    'in_array'             => ':attribute غير موجود في :other.',
    'integer'              => 'يجب أن يكون :attribute عددًا صحيحًا.',
    'ip'                   => 'يجب أن يكون :attribute عنوان IP صحيحًا.',
    'ipv4'                 => 'يجب أن يكون :attribute عنوان IPv4 صحيحًا.',
    'ipv6'                 => 'يجب أن يكون :attribute عنوان IPv6 صحيحًا.',
    'json'                 => 'يجب أن يكون :attribute نصآ من نوع JSON.',
    'lt'                   => [
        'numeric' => 'يجب أن تكون قيمة :attribute أصغر من :value.',
        'file'    => 'يجب أن يكون حجم الملف :attribute أصغر من :value كيلوبايت.',
        'string'  => 'يجب أن يكون طول النّص :attribute أقل من :value حروفٍ/حرفًا.',
        'array'   => 'يجب أن يحتوي :attribute على أقل من :value عناصر/عنصر.',
    ],
    'lte'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أصغر من :value.',
        'file'    => 'يجب أن لا يتجاوز حجم الملف :attribute :value كيلوبايت.',
        'string'  => 'يجب أن لا يتجاوز طول النّص :attribute :value حروفٍ/حرفًا.',
        'array'   => 'يجب أن لا يحتوي :attribute على أكثر من :value عناصر/عنصر.',
    ],
    'max'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أصغر من :max.',
        'file'    => 'يجب أن لا يتجاوز حجم الملف :attribute :max كيلوبايت.',
        'string'  => 'يجب أن لا يتجاوز طول النّص :attribute :max حروفٍ/حرفًا.',
        'array'   => 'يجب أن لا يحتوي :attribute على أكثر من :max عناصر/عنصر.',
    ],
    'mimes'                => 'يجب أن يكون ملفًا من نوع : :values.',
    'mimetypes'            => 'يجب أن يكون ملفًا من نوع : :values.',
    'min'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أكبر من :min.',
        'file'    => 'يجب أن يكون حجم الملف :attribute على الأقل :min كيلوبايت.',
        'string'  => 'يجب أن يكون طول النص :attribute على الأقل :min حروفٍ/حرفًا.',
        'array'   => 'يجب أن يحتوي :attribute على الأقل على :min عُنصرًا/عناصر.',
    ],
    'not_in'               => ':attribute موجود.',
    'not_regex'            => 'صيغة :attribute غير صحيحة.',
    'numeric'              => 'يجب  :attribute أن يكون رقمًا.',
    'present'              => 'يجب تقديم :attribute.',
    'regex'                => 'صيغة :attribute .غير صحيحة.',
    'required'             => ':attribute مطلوب.',
    'required_if'          => ':attribute مطلوب في حال ما إذا كان :other يساوي :value.',
    'required_unless'      => ':attribute مطلوب في حال ما لم يكن :other يساوي :values.',
    'required_with'        => ':attribute مطلوبة.',
    'required_with_all'    => ':attribute مطلوب إذا توفّر :values.',
    'required_without'     => ':attribute مطلوب إذا لم يتوفّر :values.',
    'required_without_all' => ':attribute مطلوب إذا لم يتوفّر :values.',
    'same'                 => 'يجب أن يتطابق :attribute مع :other.',
    'size'                 => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية لـ :size.',
        'file'    => 'يجب أن يكون حجم الملف :attribute :size كيلوبايت.',
        'string'  => 'يجب أن يحتوي النص :attribute على :size حروفٍ/حرفًا بالضبط.',
        'array'   => 'يجب أن يحتوي :attribute على :size عنصرٍ/عناصر بالضبط.',
    ],
    'starts_with'          => 'يجب أن يبدأ :attribute بأحد القيم التالية: :values',
    'string'               => 'يجب أن يكون :attribute نصًا.',
    'timezone'             => 'يجب أن يكون :attribute نطاقًا زمنيًا صحيحًا.',
    'unique'               => 'قيمة :attribute مُستخدمة من قبل.',
    'uploaded'             => 'فشل في تحميل الـ :attribute.',
    'url'                  => 'صيغة الرابط :attribute غير صحيحة.',
    'uuid'                 => ':attribute يجب أن يكون بصيغة UUID سليمة.',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */
    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */
    'attributes' => [
        'name'                  => 'الاسم',
        'username'              => 'اسم المُستخدم',
        'nickname'              => 'اسم الشهرة',
        'email'                 => 'البريد الالكتروني',
        'first_name'            => 'الاسم الأول',
        'last_name'             => 'اسم العائلة',
        "id_number"             => 'رقم الهوية',
        'password'              => 'كلمة المرور',
        'birth_date'            => 'تاريخ الميلاد',
        'agreement'             => 'الموافقة',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'city'                  => 'المدينة',
        'district'              => 'الحى',
        'street'                => 'الشارع',
        'country'               => 'الدولة',
        'address'               => 'عنوان السكن',
        'phone'                 => 'الهاتف',
        'mobile'                => 'رقم الجوال',
        'age'                   => 'العمر',
        'sex'                   => 'الجنس',
        'gender'                => 'الجنس',
        'day'                   => 'اليوم',
        'month'                 => 'الشهر',
        'year'                  => 'السنة',
        'hour'                  => 'ساعة',
        'minute'                => 'دقيقة',
        'second'                => 'ثانية',
        'title'                 => 'العنوان',
        'content'               => 'المُحتوى',
        'description'           => 'الوصف',
        'excerpt'               => 'المُلخص',
        'date'                  => 'التاريخ',
        'time'                  => 'الوقت',
        'available'             => 'مُتاح',
        'size'                  => 'الحجم',
        'activation_code'       => 'رمز التفعيل',
        'longitude'             => 'خط الطول',
        'latitude'              => 'خط العرض',
        'day_date'              => ' يوم الحجز ',
        'from_time'             => 'توقيت الحجز ',
        'to_time'               => 'توقيت الحجز   ',
        'doctor_id'             => 'كود الدكتور',
        'payment_method_id'     => 'كود طريقة الدفع',
        'use_insurance'         => 'استخدم التأمين',
        'paid'                  => 'مدفوع',
        'rate'                  => 'التقييم',
        'importance'            => 'الأهمية',
        'type'                  => 'النوع',
        'message'               => 'الرسالة',
        'reservation_no'        => 'رقم الحجز',
        'reservation_no'        => 'رقم الحجز',
        'reason'                => 'السبب',
        'branch_no'             => 'رقم الفرع',
        'name_en'               => 'الاسم بالإنجليزية',
        'name_ar'               => 'الاسم بالعربية',
        'city_id'               => 'كود المدينة',
        'distinct_id'           => 'كود الحى',
        'branch_id'             => 'رقم الفرع',
        'nickname_en'           => 'اللقب بالإنجليزيه',
        'nickname_ar'           => 'اللفب بالعربية',
        'specification id'      => 'كود التخصص',
        'price'                 => 'السعر',
        'information_en'        => 'المعلومات بالإنجليزيه',
        'information_ar'        => 'المعلومات بالعربية',
        'photo'                 => 'الصورة',
        'insurance_companies'   => 'شركات التأمين',
        'working_days'          => 'ايام العمل',
        'nationality_id'        => 'كود الجنسية',
        'reservation_period'    => 'مدة الحجز',
        'commercial_no'         => 'الرقم التجارى',
        'nickname_id'           => 'كود اللقب',
        'agreement_ar'          => 'نص الموافقة بالعربى',
        'agreement_en'          => 'نص الموافقة بالإنجليزية',
        'user_id'               => 'كود المستخدم',
        'reservation_id'        => 'كود الحجز',
        'attachments'           => 'المرفقات',
        'mobile_id'             => 'رقم الجوال او الهوية',
        'doctor_rate'           => 'تقييم الدكتور',
        'provider_rate'         => 'تقييم مقدم الخدمة',
        'message_id'            => 'كود الرسالة',
        'type_id'               => 'كود النوع',
        'hide'                   => 'حالة الإخفاء',
        'insurance_expire_date'  => 'تاريخ انتهاء التأمين ',
        'old_password'           => 'كلمة المرور الحالية',
        'invited_user_mobile'    => 'رقم هاتف  الشخص المدعو',
        'current_mobile'         => 'الهاتف الحالي ',
        'points'                 => 'عدد النقاط ',
        'query'                  => 'الجملة المدخلة',
        'insurance_image'=>'صوره التامين ',
        'rate_comment'   => 'نص التقييم',
        'duration'    => 'عدد الايام ',
        'title_ar'     => 'العنوان بالعربي',
        'title_en'     => 'العنوان بالانجليزية',
        'category_id' => 'القسم ',
        'coupons_type_id' => 'نوع الكوبون  ',
        'available_count' => 'العدد المسموح به',
        'status'   => 'الحالة',
        'provider_id' => 'مقدم الخدمة ',
        'features_ar'  => 'الوصف بالعربي ',
        'features_en'  => 'الوصف بالانجليزي ',
        'amount' => 'الكمية',
        'code'  => 'رمز الكوبون',
        'hours' => 'عدد الساعات',
        'minutes' => 'عدد الدقائق',
        'seconds' => 'عدد الثواني',
        'price_after_discount'  => 'السعر بعد الخصم ',
        'price_less' => 'السعر',
        'point_price' => 'سعر النقطة',
        'bank_fees' => 'أتعاب البنك  ',
        'owner_points'  => 'نقاط صاحب الكود ',
        'invited_points'  => 'نقاط المدعو  ',

        'commercial_ar'  => 'الاسم التجاري بالعربى',
        'commercial_en'  => 'الاسم التجاري بالإنجليزي',
        'district_id'  => 'الحى',
        'application_percentage'  => 'نسبة التطبيق علي الكشف',
        'image'  => 'الصورة',
        'notify-type'  => 'نوع الإشعار',
        'gift_id'  => 'رقم الهدية',

    ],
];
