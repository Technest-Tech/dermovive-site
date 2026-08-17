<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the three legal CMS pages (Refund Policy, Privacy Policy, Terms of
 * Service). Idempotent (updateOrInsert by slug) so it is safe to re-run and
 * the pages stay editable afterwards in the Filament admin. Bodies are English
 * for every locale; titles are localised.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        foreach ($this->pages() as $page) {
            DB::table('pages')->updateOrInsert(
                ['slug' => $page['slug']],
                [
                    'title' => json_encode($page['title'], JSON_UNESCAPED_UNICODE),
                    'body' => json_encode($page['body'], JSON_UNESCAPED_UNICODE),
                    'meta_title' => json_encode($page['meta_title'], JSON_UNESCAPED_UNICODE),
                    'meta_description' => json_encode($page['meta_description'], JSON_UNESCAPED_UNICODE),
                    'is_published' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }

    public function down(): void
    {
        DB::table('pages')
            ->whereIn('slug', ['refund-policy', 'privacy-policy', 'terms-of-service'])
            ->delete();
    }

    /** @return array<int, array<string, mixed>> */
    private function pages(): array
    {
        $refund = $this->refundBody();
        $privacy = $this->privacyBody();
        $terms = $this->termsBody();

        return [
            [
                'slug' => 'refund-policy',
                'title' => [
                    'en' => 'Refund Policy',
                    'ar' => 'سياسة الاسترجاع',
                    'fr' => 'Politique de remboursement',
                ],
                'body' => ['en' => $refund, 'ar' => $refund, 'fr' => $refund],
                'meta_title' => [
                    'en' => 'Refund Policy',
                    'ar' => 'سياسة الاسترجاع',
                    'fr' => 'Politique de remboursement',
                ],
                'meta_description' => [
                    'en' => 'Dermovive Pharma refund and return policy: 30-day returns, eligibility, exchanges and refund timelines.',
                    'ar' => 'سياسة الاسترجاع والإرجاع لدى ديرموفيف فارما.',
                    'fr' => 'Politique de remboursement et de retour de Dermovive Pharma.',
                ],
            ],
            [
                'slug' => 'privacy-policy',
                'title' => [
                    'en' => 'Privacy Policy',
                    'ar' => 'سياسة الخصوصية',
                    'fr' => 'Politique de confidentialité',
                ],
                'body' => ['en' => $privacy, 'ar' => $privacy, 'fr' => $privacy],
                'meta_title' => [
                    'en' => 'Privacy Policy',
                    'ar' => 'سياسة الخصوصية',
                    'fr' => 'Politique de confidentialité',
                ],
                'meta_description' => [
                    'en' => 'How Dermovive Pharma collects, uses, and discloses your personal information.',
                    'ar' => 'كيف تجمع ديرموفيف فارما معلوماتك الشخصية وتستخدمها.',
                    'fr' => 'Comment Dermovive Pharma collecte et utilise vos données personnelles.',
                ],
            ],
            [
                'slug' => 'terms-of-service',
                'title' => [
                    'en' => 'Terms of Service',
                    'ar' => 'شروط الخدمة',
                    'fr' => 'Conditions d\'utilisation',
                ],
                'body' => ['en' => $terms, 'ar' => $terms, 'fr' => $terms],
                'meta_title' => [
                    'en' => 'Terms of Service',
                    'ar' => 'شروط الخدمة',
                    'fr' => 'Conditions d\'utilisation',
                ],
                'meta_description' => [
                    'en' => 'The terms and conditions governing your use of the Dermovive Pharma website and services.',
                    'ar' => 'الشروط والأحكام التي تحكم استخدامك لموقع ديرموفيف فارما.',
                    'fr' => 'Les conditions régissant votre utilisation du site Dermovive Pharma.',
                ],
            ],
        ];
    }

    private function refundBody(): string
    {
        return <<<'HTML'
<p>We have a 30-day return policy, which means you have 30 days after receiving your item to request a return.</p>
<p>To be eligible for a return, your item must be in the same condition that you received it, unworn or unused, with tags, and in its original packaging. You'll also need the receipt or proof of purchase.</p>
<p>To start a return, you can contact us at <a href="tel:01002058424">01002058424</a>. Please note that returns will need to be sent to the following address: Nasr City, Cairo, Egypt.</p>
<p>If your return is accepted, we'll send you a return shipping label, as well as instructions on how and where to send your package. Items sent back to us without first requesting a return will not be accepted.</p>
<p>You can always contact us for any return question at <a href="tel:01002058424">01002058424</a>.</p>
<h2>Damages and issues</h2>
<p>Please inspect your order upon reception and contact us immediately if the item is defective, damaged or if you receive the wrong item, so that we can evaluate the issue and make it right.</p>
<h2>Exceptions / non-returnable items</h2>
<p>Certain types of items cannot be returned, like perishable goods (such as food, flowers, or plants), custom products (such as special orders or personalized items), and personal care goods (such as beauty products). We also do not accept returns for hazardous materials, flammable liquids, or gases. Please get in touch if you have questions or concerns about your specific item.</p>
<p>Unfortunately, we cannot accept returns on sale items or gift cards.</p>
<h2>Exchanges</h2>
<p>The fastest way to ensure you get what you want is to return the item you have, and once the return is accepted, make a separate purchase for the new item.</p>
<h2>European Union 14 day cooling off period</h2>
<p>Notwithstanding the above, if the merchandise is being shipped into the European Union, you have the right to cancel or return your order within 14 days, for any reason and without a justification. As above, your item must be in the same condition that you received it, unworn or unused, with tags, and in its original packaging. You'll also need the receipt or proof of purchase.</p>
<h2>Refunds</h2>
<p>We will notify you once we've received and inspected your return, and let you know if the refund was approved or not. If approved, you'll be automatically refunded on your original payment method within 10 business days. Please remember it can take some time for your bank or credit card company to process and post the refund too.</p>
<p>If more than 15 business days have passed since we've approved your return, please contact us at <a href="tel:01002058424">01002058424</a>.</p>
HTML;
    }

    private function privacyBody(): string
    {
        return <<<'HTML'
<p><strong>Last updated:</strong> 17 August 2026</p>
<p>This Privacy Policy describes how Dermovive Pharma (the "Site", "we", "us", or "our") collects, uses, and discloses your personal information when you visit, use our services, or make a purchase from dermovive-pharma.com (the "Site") or otherwise communicate with us (collectively, the "Services"). For purposes of this Privacy Policy, "you" and "your" means you as the user of the Services, whether you are a customer, website visitor, or another individual whose information we have collected pursuant to this Privacy Policy.</p>
<p>Please read this Privacy Policy carefully. By using and accessing any of the Services, you agree to the collection, use, and disclosure of your information as described in this Privacy Policy. If you do not agree to this Privacy Policy, please do not use or access any of the Services.</p>
<h2>Changes to This Privacy Policy</h2>
<p>We may update this Privacy Policy from time to time, including to reflect changes to our practices or for other operational, legal, or regulatory reasons. We will post the revised Privacy Policy on the Site, update the "Last updated" date and take any other steps required by applicable law.</p>
<h2>How We Collect and Use Your Personal Information</h2>
<p>To provide the Services, we collect and have collected over the past 12 months personal information about you from a variety of sources, as set out below. The information that we collect and use varies depending on how you interact with us.</p>
<p>In addition to the specific uses set out below, we may use information we collect about you to communicate with you, provide the Services, comply with any applicable legal obligations, enforce any applicable terms of service, and to protect or defend the Services, our rights, and the rights of our users or others.</p>
<h2>What Personal Information We Collect</h2>
<p>The types of personal information we obtain about you depends on how you interact with our Site and use our Services. When we use the term "personal information", we are referring to information that identifies, relates to, describes or can be associated with you. The following sections describe the categories and specific types of personal information we collect.</p>
<h3>Information We Collect Directly from You</h3>
<p>Information that you directly submit to us through our Services may include:</p>
<ul>
<li>Basic contact details including your name, address, phone number, email.</li>
<li>Order information including your name, billing address, shipping address, payment confirmation, email address, phone number.</li>
<li>Account information including your username, password, security questions.</li>
<li>Shopping information including the items you view, put in your cart or add to your wishlist.</li>
<li>Customer support information including the information you choose to include in communications with us, for example, when sending a message through the Services.</li>
</ul>
<p>Some features of the Services may require you to directly provide us with certain information about yourself. You may elect not to provide this information, but doing so may prevent you from using or accessing these features.</p>
<h3>Information We Collect through Cookies</h3>
<p>We also automatically collect certain information about your interaction with the Services ("Usage Data"). To do this, we may use cookies, pixels and similar technologies ("Cookies"). Usage Data may include information about how you access and use our Site and your account, including device information, browser information, information about your network connection, your IP address and other information regarding your interaction with the Services.</p>
<h3>Information We Obtain from Third Parties</h3>
<p>Finally, we may obtain information about you from third parties, including from vendors and service providers who may collect information on our behalf, such as:</p>
<ul>
<li>Companies who support our Site and Services, such as our hosting and infrastructure providers.</li>
<li>Our payment processors, who collect payment information (e.g., bank account, credit or debit card information, billing address) to process your payment in order to fulfill your orders and provide you with products or services you have requested, in order to perform our contract with you.</li>
<li>When you visit our Site, open or click on emails we send you, or interact with our Services or advertisements, we, or third parties we work with, may automatically collect certain information using online tracking technologies such as pixels, web beacons, software developer kits, third-party libraries, and cookies.</li>
</ul>
<p>Any information we obtain from third parties will be treated in accordance with this Privacy Policy. We are not responsible or liable for the accuracy of the information provided to us by third parties and are not responsible for any third party's policies or practices. For more information, see the section below, Third Party Websites and Links.</p>
<h2>How We Use Your Personal Information</h2>
<ul>
<li><strong>Providing Products and Services.</strong> We use your personal information to provide you with the Services in order to perform our contract with you, including to process your payments, fulfill your orders, to send notifications to you related to your account, purchases, returns, exchanges or other transactions, to create, maintain and otherwise manage your account, to arrange for shipping, facilitate any returns and exchanges and to enable you to post reviews.</li>
<li><strong>Marketing and Advertising.</strong> We use your personal information for marketing and promotional purposes, such as to send marketing, advertising and promotional communications by email, text message or postal mail, and to show you advertisements for products or services. This may include using your personal information to better tailor the Services and advertising on our Site and other websites.</li>
<li><strong>Security and Fraud Prevention.</strong> We use your personal information to detect, investigate or take action regarding possible fraudulent, illegal or malicious activity. If you choose to use the Services and register an account, you are responsible for keeping your account credentials safe. We highly recommend that you do not share your username, password, or other access details with anyone else. If you believe your account has been compromised, please contact us immediately.</li>
<li><strong>Communicating with you.</strong> We use your personal information to provide you with customer support and improve our Services. This is in our legitimate interests in order to be responsive to you, to provide effective services to you, and to maintain our business relationship with you.</li>
</ul>
<h2>Cookies</h2>
<p>Like many websites, we use Cookies on our Site. We use Cookies to power and improve our Site and our Services (including to remember your actions and preferences), to run analytics and better understand user interaction with the Services (in our legitimate interests to administer, improve and optimize the Services). We may also permit third parties and service providers to use Cookies on our Site to better tailor the services, products and advertising on our Site and other websites.</p>
<p>Most browsers automatically accept Cookies by default, but you can choose to set your browser to remove or reject Cookies through your browser controls. Please keep in mind that removing or blocking Cookies can negatively impact your user experience and may cause some of the Services, including certain features and general functionality, to work incorrectly or no longer be available. Additionally, blocking Cookies may not completely prevent how we share information with third parties such as our advertising partners.</p>
<h2>How We Disclose Personal Information</h2>
<p>In certain circumstances, we may disclose your personal information to third parties for legitimate purposes subject to this Privacy Policy. Such circumstances may include:</p>
<ul>
<li>With vendors or other third parties who perform services on our behalf (e.g., IT management, payment processing, data analytics, customer support, cloud storage, fulfillment and shipping).</li>
<li>With business and marketing partners, to provide services and advertise to you. Our business and marketing partners will use your information in accordance with their own privacy notices.</li>
<li>When you direct, request us or otherwise consent to our disclosure of certain information to third parties, such as to ship you products or through your use of social media widgets or login integrations, with your consent.</li>
<li>With our affiliates or otherwise within our corporate group, in our legitimate interests to run a successful business.</li>
<li>In connection with a business transaction such as a merger or bankruptcy, to comply with any applicable legal obligations (including to respond to subpoenas, search warrants and similar requests), to enforce any applicable terms of service, and to protect or defend the Services, our rights, and the rights of our users or others.</li>
</ul>
<p>We have, in the past 12 months, disclosed the following categories of personal information about users for the purposes set out above in "How We Collect and Use Your Personal Information" and "How We Disclose Personal Information":</p>
<ul>
<li><strong>Identifiers</strong> such as basic contact details and certain order and account information — disclosed to vendors and third parties who perform services on our behalf (such as Internet service providers, payment processors, fulfillment partners, customer support partners and data analytics providers), business and marketing partners, and affiliates.</li>
<li><strong>Commercial information</strong> such as order information, shopping information and customer support information — disclosed to the same categories of recipients.</li>
<li><strong>Internet or other similar network activity</strong>, such as Usage Data — disclosed to the same categories of recipients.</li>
</ul>
<p>We do not use or disclose sensitive personal information for the purposes of inferring characteristics about you.</p>
<h2>User Generated Content</h2>
<p>The Services may enable you to post product reviews and other user-generated content. If you choose to submit user generated content to any public area of the Services, this content will be public and accessible by anyone.</p>
<p>We do not control who will have access to the information that you choose to make available to others, and cannot ensure that parties who have access to such information will respect your privacy or keep it secure. We are not responsible for the privacy or security of any information that you make publicly available, or for the accuracy, use or misuse of any information that you disclose or receive from third parties.</p>
<h2>Third Party Websites and Links</h2>
<p>Our Site may provide links to websites or other online platforms operated by third parties. If you follow links to sites not affiliated or controlled by us, you should review their privacy and security policies and other terms and conditions. We do not guarantee and are not responsible for the privacy or security of such sites, including the accuracy, completeness, or reliability of information found on these sites. Information you provide on public or semi-public venues, including information you share on third-party social networking platforms may also be viewable by other users of the Services and/or users of those third-party platforms without limitation as to its use by us or by a third party. Our inclusion of such links does not, by itself, imply any endorsement of the content on such platforms or of their owners or operators, except as disclosed on the Services.</p>
<h2>Security and Retention of Your Information</h2>
<p>Please be aware that no security measures are perfect or impenetrable, and we cannot guarantee "perfect security." In addition, any information you send to us may not be secure while in transit. We recommend that you do not use unsecure channels to communicate sensitive or confidential information to us.</p>
<p>How long we retain your personal information depends on different factors, such as whether we need the information to maintain your account, to provide the Services, comply with legal obligations, resolve disputes or enforce other applicable contracts and policies.</p>
<h2>Your Rights and Choices</h2>
<p>Depending on where you live, you may have some or all of the rights listed below in relation to your personal information. However, these rights are not absolute, may apply only in certain circumstances and, in certain cases, we may decline your request as permitted by law.</p>
<ul>
<li><strong>Right to Access / Know.</strong> You may have a right to request access to personal information that we hold about you, including details relating to the ways in which we use and share your information.</li>
<li><strong>Right to Delete.</strong> You may have a right to request that we delete personal information we maintain about you.</li>
<li><strong>Right to Correct.</strong> You may have a right to request that we correct inaccurate personal information we maintain about you.</li>
<li><strong>Right of Portability.</strong> You may have a right to receive a copy of the personal information we hold about you and to request that we transfer it to a third party, in certain circumstances and with certain exceptions.</li>
<li><strong>Restriction of Processing.</strong> You may have the right to ask us to stop or restrict our processing of personal information.</li>
<li><strong>Withdrawal of Consent.</strong> Where we rely on consent to process your personal information, you may have the right to withdraw this consent.</li>
<li><strong>Appeal.</strong> You may have a right to appeal our decision if we decline to process your request. You can do so by replying directly to our denial.</li>
<li><strong>Managing Communication Preferences.</strong> We may send you promotional emails, and you may opt out of receiving these at any time by using the unsubscribe option displayed in our emails to you. If you opt out, we may still send you non-promotional emails, such as those about your account or orders that you have made.</li>
</ul>
<p>You may exercise any of these rights where indicated on our Site or by contacting us using the contact details provided below.</p>
<p>We will not discriminate against you for exercising any of these rights. We may need to collect information from you to verify your identity, such as your email address or account information, before providing a substantive response to the request. In accordance with applicable laws, you may designate an authorized agent to make requests on your behalf to exercise your rights. Before accepting such a request from an agent, we will require that the agent provide proof you have authorized them to act on your behalf, and we may need you to verify your identity directly with us. We will respond to your request in a timely manner as required under applicable law.</p>
<h2>Complaints</h2>
<p>If you have complaints about how we process your personal information, please contact us using the contact details provided below. If you are not satisfied with our response to your complaint, depending on where you live you may have the right to appeal our decision by contacting us using the contact details set out below, or lodge your complaint with your local data protection authority.</p>
<h2>International Users</h2>
<p>Please note that we may transfer, store and process your personal information outside the country you live in. Your personal information is also processed by staff and third party service providers and partners in these countries.</p>
<p>If we transfer your personal information out of Europe, we will rely on recognized transfer mechanisms like the European Commission's Standard Contractual Clauses, or any equivalent contracts issued by the relevant competent authority of the UK, as relevant, unless the data transfer is to a country that has been determined to provide an adequate level of protection.</p>
<h2>Contact</h2>
<p>Should you have any questions about our privacy practices or this Privacy Policy, or if you would like to exercise any of the rights available to you, please email us at <a href="mailto:dermovivepharmasn@gmail.com">dermovivepharmasn@gmail.com</a>, call us at <a href="tel:01002058424">01002058424</a>, or write to us at Nasr City, Cairo, Egypt.</p>
HTML;
    }

    private function termsBody(): string
    {
        return <<<'HTML'
<h2>Overview</h2>
<p>This website is operated by Dermovive Pharma. Throughout the site, the terms "we", "us" and "our" refer to Dermovive Pharma. Dermovive Pharma offers this website, including all information, tools and Services available from this site to you, the user, conditioned upon your acceptance of all terms, conditions, policies and notices stated here.</p>
<p>By visiting our site and/or purchasing something from us, you engage in our "Service" and agree to be bound by the following terms and conditions ("Terms of Service", "Terms"), including those additional terms and conditions and policies referenced herein and/or available by hyperlink. These Terms of Service apply to all users of the site, including without limitation users who are browsers, vendors, customers, merchants, and/or contributors of content.</p>
<p>Please read these Terms of Service carefully before accessing or using our website. By accessing or using any part of the site, you agree to be bound by these Terms of Service. If you do not agree to all the terms and conditions of this agreement, then you may not access the website or use any Services. If these Terms of Service are considered an offer, acceptance is expressly limited to these Terms of Service.</p>
<p>Any new features or tools which are added to the current store shall also be subject to the Terms of Service. You can review the most current version of the Terms of Service at any time on this page. We reserve the right to update, change or replace any part of these Terms of Service by posting updates and/or changes to our website. It is your responsibility to check this page periodically for changes. Your continued use of or access to the website following the posting of any changes constitutes acceptance of those changes.</p>
<h2>Section 1 - Online Store Terms</h2>
<p>By agreeing to these Terms of Service, you represent that you are at least the age of majority in your state or province of residence, or that you are the age of majority in your state or province of residence and you have given us your consent to allow any of your minor dependents to use this site.</p>
<p>You may not use our products for any illegal or unauthorized purpose nor may you, in the use of the Service, violate any laws in your jurisdiction (including but not limited to copyright laws).</p>
<p>You must not transmit any worms or viruses or any code of a destructive nature.</p>
<p>A breach or violation of any of the Terms will result in an immediate termination of your Services.</p>
<h2>Section 2 - General Conditions</h2>
<p>We reserve the right to refuse Service to anyone for any reason at any time.</p>
<p>You understand that your content (not including credit card information), may be transferred unencrypted and involve (a) transmissions over various networks; and (b) changes to conform and adapt to technical requirements of connecting networks or devices. Credit card information is always encrypted during transfer over networks.</p>
<p>You agree not to reproduce, duplicate, copy, sell, resell or exploit any portion of the Service, use of the Service, or access to the Service or any contact on the website through which the Service is provided, without express written permission by us.</p>
<p>The headings used in this agreement are included for convenience only and will not limit or otherwise affect these Terms.</p>
<h2>Section 3 - Accuracy, Completeness and Timeliness of Information</h2>
<p>We are not responsible if information made available on this site is not accurate, complete or current. The material on this site is provided for general information only and should not be relied upon or used as the sole basis for making decisions without consulting primary, more accurate, more complete or more timely sources of information. Any reliance on the material on this site is at your own risk.</p>
<p>This site may contain certain historical information. Historical information, necessarily, is not current and is provided for your reference only. We reserve the right to modify the contents of this site at any time, but we have no obligation to update any information on our site. You agree that it is your responsibility to monitor changes to our site.</p>
<h2>Section 4 - Modifications to the Service and Prices</h2>
<p>Prices for our products are subject to change without notice.</p>
<p>We reserve the right at any time to modify or discontinue the Service (or any part or content thereof) without notice at any time.</p>
<p>We shall not be liable to you or to any third-party for any modification, price change, suspension or discontinuance of the Service.</p>
<h2>Section 5 - Products or Services (if applicable)</h2>
<p>Certain products or Services may be available exclusively online through the website. These products or Services may have limited quantities and are subject to return or exchange only according to our <a href="/refund-policy">Refund Policy</a>.</p>
<p>We have made every effort to display as accurately as possible the colors and images of our products that appear at the store. We cannot guarantee that your computer monitor's display of any color will be accurate.</p>
<p>We reserve the right, but are not obligated, to limit the sales of our products or Services to any person, geographic region or jurisdiction. We may exercise this right on a case-by-case basis. We reserve the right to limit the quantities of any products or Services that we offer. All descriptions of products or product pricing are subject to change at anytime without notice, at the sole discretion of us. We reserve the right to discontinue any product at any time. Any offer for any product or Service made on this site is void where prohibited.</p>
<p>We do not warrant that the quality of any products, Services, information, or other material purchased or obtained by you will meet your expectations, or that any errors in the Service will be corrected.</p>
<h2>Section 6 - Accuracy of Billing and Account Information</h2>
<p>We reserve the right to refuse any order you place with us. We may, in our sole discretion, limit or cancel quantities purchased per person, per household or per order. These restrictions may include orders placed by or under the same customer account, the same credit card, and/or orders that use the same billing and/or shipping address. In the event that we make a change to or cancel an order, we may attempt to notify you by contacting the e-mail and/or billing address/phone number provided at the time the order was made. We reserve the right to limit or prohibit orders that, in our sole judgment, appear to be placed by dealers, resellers or distributors.</p>
<p>You agree to provide current, complete and accurate purchase and account information for all purchases made at our store. You agree to promptly update your account and other information, including your email address and credit card numbers and expiration dates, so that we can complete your transactions and contact you as needed.</p>
<p>For more details, please review our <a href="/refund-policy">Refund Policy</a>.</p>
<h2>Section 7 - Optional Tools</h2>
<p>We may provide you with access to third-party tools over which we neither monitor nor have any control nor input.</p>
<p>You acknowledge and agree that we provide access to such tools "as is" and "as available" without any warranties, representations or conditions of any kind and without any endorsement. We shall have no liability whatsoever arising from or relating to your use of optional third-party tools.</p>
<p>Any use by you of the optional tools offered through the site is entirely at your own risk and discretion and you should ensure that you are familiar with and approve of the terms on which tools are provided by the relevant third-party provider(s).</p>
<p>We may also, in the future, offer new Services and/or features through the website (including the release of new tools and resources). Such new features and/or Services shall also be subject to these Terms of Service.</p>
<h2>Section 8 - Third-Party Links</h2>
<p>Certain content, products and Services available via our Service may include materials from third-parties.</p>
<p>Third-party links on this site may direct you to third-party websites that are not affiliated with us. We are not responsible for examining or evaluating the content or accuracy and we do not warrant and will not have any liability or responsibility for any third-party materials or websites, or for any other materials, products, or Services of third-parties.</p>
<p>We are not liable for any harm or damages related to the purchase or use of goods, Services, resources, content, or any other transactions made in connection with any third-party websites. Please review carefully the third-party's policies and practices and make sure you understand them before you engage in any transaction. Complaints, claims, concerns, or questions regarding third-party products should be directed to the third-party.</p>
<h2>Section 9 - User Comments, Feedback and Other Submissions</h2>
<p>If, at our request, you send certain specific submissions (for example contest entries) or without a request from us, you send creative ideas, suggestions, proposals, plans, or other materials, whether online, by email, by postal mail, or otherwise (collectively, 'comments'), you agree that we may, at any time, without restriction, edit, copy, publish, distribute, translate and otherwise use in any medium any comments that you forward to us. We are and shall be under no obligation (1) to maintain any comments in confidence; (2) to pay compensation for any comments; or (3) to respond to any comments.</p>
<p>We may, but have no obligation to, monitor, edit or remove content that we determine in our sole discretion to be unlawful, offensive, threatening, libelous, defamatory, pornographic, obscene or otherwise objectionable or violates any party's intellectual property or these Terms of Service.</p>
<p>You agree that your comments will not violate any right of any third-party, including copyright, trademark, privacy, personality or other personal or proprietary right. You further agree that your comments will not contain libelous or otherwise unlawful, abusive or obscene material, or contain any computer virus or other malware that could in any way affect the operation of the Service or any related website. You may not use a false e-mail address, pretend to be someone other than yourself, or otherwise mislead us or third-parties as to the origin of any comments. You are solely responsible for any comments you make and their accuracy. We take no responsibility and assume no liability for any comments posted by you or any third-party.</p>
<h2>Section 10 - Personal Information</h2>
<p>Your submission of personal information through the store is governed by our <a href="/privacy-policy">Privacy Policy</a>.</p>
<h2>Section 11 - Errors, Inaccuracies and Omissions</h2>
<p>Occasionally there may be information on our site or in the Service that contains typographical errors, inaccuracies or omissions that may relate to product descriptions, pricing, promotions, offers, product shipping charges, transit times and availability. We reserve the right to correct any errors, inaccuracies or omissions, and to change or update information or cancel orders if any information in the Service or on any related website is inaccurate at any time without prior notice (including after you have submitted your order).</p>
<p>We undertake no obligation to update, amend or clarify information in the Service or on any related website, including without limitation, pricing information, except as required by law. No specified update or refresh date applied in the Service or on any related website, should be taken to indicate that all information in the Service or on any related website has been modified or updated.</p>
<h2>Section 12 - Prohibited Uses</h2>
<p>In addition to other prohibitions as set forth in the Terms of Service, you are prohibited from using the site or its content: (a) for any unlawful purpose; (b) to solicit others to perform or participate in any unlawful acts; (c) to violate any international, federal, provincial or state regulations, rules, laws, or local ordinances; (d) to infringe upon or violate our intellectual property rights or the intellectual property rights of others; (e) to harass, abuse, insult, harm, defame, slander, disparage, intimidate, or discriminate based on gender, sexual orientation, religion, ethnicity, race, age, national origin, or disability; (f) to submit false or misleading information; (g) to upload or transmit viruses or any other type of malicious code that will or may be used in any way that will affect the functionality or operation of the Service or of any related website, other websites, or the Internet; (h) to collect or track the personal information of others; (i) to spam, phish, pharm, pretext, spider, crawl, or scrape; (j) for any obscene or immoral purpose; or (k) to interfere with or circumvent the security features of the Service or any related website, other websites, or the Internet. We reserve the right to terminate your use of the Service or any related website for violating any of the prohibited uses.</p>
<h2>Section 13 - Disclaimer of Warranties; Limitation of Liability</h2>
<p>We do not guarantee, represent or warrant that your use of our Service will be uninterrupted, timely, secure or error-free.</p>
<p>We do not warrant that the results that may be obtained from the use of the Service will be accurate or reliable.</p>
<p>You agree that from time to time we may remove the Service for indefinite periods of time or cancel the Service at any time, without notice to you.</p>
<p>You expressly agree that your use of, or inability to use, the Service is at your sole risk. The Service and all products and Services delivered to you through the Service are (except as expressly stated by us) provided 'as is' and 'as available' for your use, without any representation, warranties or conditions of any kind, either express or implied, including all implied warranties or conditions of merchantability, merchantable quality, fitness for a particular purpose, durability, title, and non-infringement.</p>
<p>In no case shall Dermovive Pharma, our directors, officers, employees, affiliates, agents, contractors, interns, suppliers, Service providers or licensors be liable for any injury, loss, claim, or any direct, indirect, incidental, punitive, special, or consequential damages of any kind, including, without limitation lost profits, lost revenue, lost savings, loss of data, replacement costs, or any similar damages, whether based in contract, tort (including negligence), strict liability or otherwise, arising from your use of any of the Service or any products procured using the Service, or for any other claim related in any way to your use of the Service or any product, including, but not limited to, any errors or omissions in any content, or any loss or damage of any kind incurred as a result of the use of the Service or any content (or product) posted, transmitted, or otherwise made available via the Service, even if advised of their possibility. Because some states or jurisdictions do not allow the exclusion or the limitation of liability for consequential or incidental damages, in such states or jurisdictions, our liability shall be limited to the maximum extent permitted by law.</p>
<h2>Section 14 - Indemnification</h2>
<p>You agree to indemnify, defend and hold harmless Dermovive Pharma and our parent, subsidiaries, affiliates, partners, officers, directors, agents, contractors, licensors, Service providers, subcontractors, suppliers, interns and employees, harmless from any claim or demand, including reasonable attorneys' fees, made by any third-party due to or arising out of your breach of these Terms of Service or the documents they incorporate by reference, or your violation of any law or the rights of a third-party.</p>
<h2>Section 15 - Severability</h2>
<p>In the event that any provision of these Terms of Service is determined to be unlawful, void or unenforceable, such provision shall nonetheless be enforceable to the fullest extent permitted by applicable law, and the unenforceable portion shall be deemed to be severed from these Terms of Service, such determination shall not affect the validity and enforceability of any other remaining provisions.</p>
<h2>Section 16 - Termination</h2>
<p>The obligations and liabilities of the parties incurred prior to the termination date shall survive the termination of this agreement for all purposes.</p>
<p>These Terms of Service are effective unless and until terminated by either you or us. You may terminate these Terms of Service at any time by notifying us that you no longer wish to use our Services, or when you cease using our site.</p>
<p>If in our sole judgment you fail, or we suspect that you have failed, to comply with any term or provision of these Terms of Service, we also may terminate this agreement at any time without notice and you will remain liable for all amounts due up to and including the date of termination; and/or accordingly may deny you access to our Services (or any part thereof).</p>
<h2>Section 17 - Entire Agreement</h2>
<p>The failure of us to exercise or enforce any right or provision of these Terms of Service shall not constitute a waiver of such right or provision.</p>
<p>These Terms of Service and any policies or operating rules posted by us on this site or in respect to the Service constitutes the entire agreement and understanding between you and us and governs your use of the Service, superseding any prior or contemporaneous agreements, communications and proposals, whether oral or written, between you and us (including, but not limited to, any prior versions of the Terms of Service).</p>
<p>Any ambiguities in the interpretation of these Terms of Service shall not be construed against the drafting party.</p>
<h2>Section 18 - Governing Law</h2>
<p>These Terms of Service and any separate agreements whereby we provide you Services shall be governed by and construed in accordance with the laws of Egypt.</p>
<h2>Section 19 - Changes to Terms of Service</h2>
<p>You can review the most current version of the Terms of Service at any time at this page.</p>
<p>We reserve the right, at our sole discretion, to update, change or replace any part of these Terms of Service by posting updates and changes to our website. It is your responsibility to check our website periodically for changes. Your continued use of or access to our website or the Service following the posting of any changes to these Terms of Service constitutes acceptance of those changes.</p>
<h2>Section 20 - Contact Information</h2>
<p>Questions about the Terms of Service should be sent to us at <a href="mailto:dermovivepharmasn@gmail.com">dermovivepharmasn@gmail.com</a>.</p>
<p>Our contact information is posted below:</p>
<p>Dermovive Pharma<br>01002058424<br>Nasr City, Cairo, Egypt</p>
HTML;
    }
};
