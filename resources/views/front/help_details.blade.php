@extends('front.layout.master')                

@section('main_content')

 <div class="blank-div"></div>
<div class="email-block">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr" >
                        {{-- @if(isset($page_slug) && $page_slug == 'user-account-and-payment')
                           User's Account and Payment Options
                        @elseif(isset($page_slug) && $page_slug == 'user-quick-pick-guide')
                            User’s Guide to QuickPick
                        @elseif(isset($page_slug) && $page_slug == 'user-signing-up')
                            User's Signing Up
                        @elseif(isset($page_slug) && $page_slug == 'user-accessibility')
                            User’s Accessibility
                        @elseif(isset($page_slug) && $page_slug == 'user-faq')
                            User’s Frequently Asked Questions and Concerns
                        @elseif(isset($page_slug) && $page_slug == 'driver-account-and-payment')
                            Driver's Account and Payment Options
                        @elseif(isset($page_slug) && $page_slug == 'driver-accessibility')
                            Driver's Accessibility
                        @elseif(isset($page_slug) && $page_slug == 'driver-quick-pick-guide')
                            Driver's Guide to QuickPick
                        @elseif(isset($page_slug) && $page_slug == 'driver-faq')
                            Driver's Frequently Asked Questions and Concerns
                        @endif --}}
                        Help Details
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ url('/')}}" class="bredcrum-home"> Home </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        <a href="{{ url('/help?type='.$selected_tab)}}" class="bredcrum-home"> Help </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        @if(isset($page_slug) && $page_slug == 'user-account-and-payment')
                            Account and Payment Options
                        @elseif(isset($page_slug) && $page_slug == 'user-quick-pick-guide')
                            User’s Guide to QuickPick
                        @elseif(isset($page_slug) && $page_slug == 'user-signing-up')
                            User Signing Up
                        @elseif(isset($page_slug) && $page_slug == 'user-accessibility')
                            User’s Accessibility
                        @elseif(isset($page_slug) && $page_slug == 'user-faq')
                            User’s Frequently Asked Questions and Concerns
                        @elseif(isset($page_slug) && $page_slug == 'driver-account-and-payment')
                            Driver's Account and Payment Options
                        @elseif(isset($page_slug) && $page_slug == 'driver-accessibility')
                            Driver's Accessibility
                        @elseif(isset($page_slug) && $page_slug == 'driver-quick-pick-guide')
                            Driver's Guide to QuickPick
                        @elseif(isset($page_slug) && $page_slug == 'driver-faq')
                            Driver's Frequently Asked Questions and Concerns
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="container">
    <div class="terms-conditions">
        <div class="terms-title">
        
            @if(isset($page_slug) && $page_slug == 'user-account-and-payment')
                Account and Payment Options
            @elseif(isset($page_slug) && $page_slug == 'user-quick-pick-guide')
                User’s Guide to QuickPick
            @elseif(isset($page_slug) && $page_slug == 'user-signing-up')
                User’s Signing Up
            @elseif(isset($page_slug) && $page_slug == 'user-accessibility')
                User’s Accessibility
            @elseif(isset($page_slug) && $page_slug == 'user-faq')
                User’s Frequently Asked Questions and Concerns
            @elseif(isset($page_slug) && $page_slug == 'driver-account-and-payment')
                Driver's Account and Payment Options
            @elseif(isset($page_slug) && $page_slug == 'driver-accessibility')
                Driver's Accessibility
            @elseif(isset($page_slug) && $page_slug == 'driver-quick-pick-guide')
                Driver's Guide to QuickPick
            @elseif(isset($page_slug) && $page_slug == 'driver-faq')
                Driver's Frequently Asked Questions and Concerns
            @endif

        </div>

        @if(isset($page_slug) && $page_slug == 'user-account-and-payment')
            
            <div class="terms-box">
                <div class="qustion-title">
                    How do I sign up for QuickPick?
                </div>
                <div class="qustion-ans">
                    If your business needs access to flexible same-day deliveries, it’s easy to sign up as a QuickPick User. Just download and open our app 
                    (or visit our home page), and submit your information to set up an account. After you sign up, you’ll be prompted to fill out a profile 
                    and select a payment method. It doesn’t get much simpler than that!
                </div>
            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    What does it cost to use QuickPick?
                </div>
                <div class="qustion-ans">
                    There is no cost to sign up or to download the app! As for using the app to have a load of supplies delivered, QuickPick offers very 
                    competitive rates for same-day delivery in the D.C. metro area. By contracting our Drivers independently, we are able to maintain a 
                    large fleet at a low cost. Our reasonable delivery rates are based on load size and expected mileage, plus whether or not your 
                    Driver will require a CDL:<br><br>
                </div>
                <div class="qustion-ans">
                    Please note that these rates are subject to change. Always check current rates in the app before ordering a delivery.
                </div>
                <div class="qustion-title underline">
                    Please note that rates are subject to change at any time.
                </div>
            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    What does my payment get me?
                </div>
                <div class="qustion-ans">
                    Simple: It gets you the goods you need to keep doing work, delivered promptly and efficiently by a friendly, courteous Driver. QuickPick gives you and your team extra efficiency, by allowing you to continue working instead of taking time off to run to your supplier for materials. That means shorter turnaround times and more lucrative contracts.
                </div>
            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    What payment methods are accepted?
                </div>
                <div class="qustion-ans">
                    QuickPick accepts payment via most major credit cards. If you expect to maintain a particularly large account with us, please contact us directly to discuss other options that may be more suited to the needs of your business.
                </div>
            </div>

        @elseif(isset($page_slug) && $page_slug == 'user-quick-pick-guide')
            <div class="terms-box">
                <div class="qustion-title">
                    What information will I need to create a User account?
                </div>
                <div class="qustion-ans">
                    As a User, you will be able to place delivery orders with QuickPick. In order to expedite this process, we require the following information from all our Users:
                </div>
                <div class="terms-box-digicts">
                    <div class="qustion-ans">
                        1. Your name.
                    </div>
                    <div class="qustion-ans">
                        2. Your business’s physical address, phone number, and email address.
                    </div>
                    <div class="qustion-ans">
                        3. A payment method to be placed on file.
                    </div>
                </div>
            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    What kinds of loads can I have delivered?
                </div>
                <div class="qustion-ans">
                    Users may order delivery of most ordinary construction material using QuickPick, but there are a few exceptions. Users may <span>not</span> use QuickPick to order delivery for the following types of loads:
                </div>
                <div class="terms-box-digicts">
                    <div class="qustion-ans">
                        1. Hazardous materials, including dangerous chemicals, biological agents, and any other noxious, unstable, corrosive, or highly flammable items.
                    </div>
                    <div class="qustion-ans">
                        2. Items that require refrigeration or temperature control.
                    </div>
                    <div class="qustion-ans">
                        3. Exceptionally large loads of the type that will require a dump truck or other heavy construction vehicle. QuickPick does not retain access to such specialized equipment.
                    </div>
                    <div class="qustion-ans">
                        4. People (i.e., using QuickPick as a taxi service).
                    </div>
                    <div class="qustion-ans">
                       5. Live animals.
                    </div>
                    <div class="qustion-ans">
                        6. Prepared food and/or drink.
                    </div>
                </div>
                <div class="qustion-ans">
                    Users are responsible for ordering ahead to their suppliers, and for buying insurance on the loads they have delivered with QuickPick. Users are also responsible for unloading the shipment when it arrives on site.
                </div>

            </div>


            <div class="terms-box">
                <div class="qustion-title">
                    What do I need to provide to get a load delivered?
                </div>
                <div class="qustion-ans">
                    There are a few important steps you must take whenever you order a delivery with QuickPick:
                </div>
                <div class="terms-box-digicts">
                    <div class="qustion-ans">
                        1. Order from your supplier and let them know you will be using QuickPick to deliver the order.
                    </div>

                    <div class="qustion-ans">
                        2. Open the QuickPick app and put in a delivery request:
                    </div>
                    
                    <div class="terms-box-digicts two">
                        <div class="qustion-ans">
                            1. Ensure that your payment information is up to date.
                        </div>
                        <div class="qustion-ans">
                            2. Enter a brief description of the load to be delivered, and contact number for the driver to call if assistance is needed at the supplier to locate your load, or to let you know he has arrived at the final destination.
                        </div>
                        <div class="qustion-ans">
                          3. Enter the estimated weight and dimensions of the load. Please be as accurate as possible.
                        </div>

                        <div class="qustion-ans">
                            4. Enter the destination where you would like the load delivered, and information on when the destination site will close for the day, if applicable.
                        </div>

                        <div class="qustion-ans">
                            5. Purchase shipping insurance for the load.
                        </div>
                    </div>
                <div class="qustion-ans">
                    3. Check the app regularly for updates on the status of your load.
                </div>
                </div>
                <div class="qustion-ans">
                    QuickPick strives to keep our service simple and easy to use. We provide everything else, including the Driver, an appropriately-sized vehicle, and an estimated window of time for the arrival of your delivery.
                </div>

            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    What does the Driver need to provide?
                </div>
                <div class="qustion-ans">
                    QuickPick serves as a virtual Bill of Lading, though Users can request that their suppliers provide a physical Bill of Lading for the Driver, if need be. Other than a virtual “paper trail,” our Drivers provide the following:
                </div>
                    <div class="terms-box-digicts">
                        <div class="qustion-ans">
                            1. Safe and timely delivery of your load, using their own vehicle.
                        </div>
                        <div class="qustion-ans">
                            2. Courteous and friendly service at all points.
                        </div>
                        <div class="qustion-ans">
                            3. Prompt notifications (via the app) of delays and other unexpected occurrences.
                        </div>
                    </div>
                <div class="qustion-ans">
                    Drivers are <span>not</span> required to assist in loading or unloading cargo from their vehicle, though most will be glad to help if they can.
                </div>

            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    What if there’s a problem?
                </div>
                <div class="qustion-ans">
                    The app is your best way to send and receive updates on delays and other delivery issues. Notifications will arrive in your inbox as they are received. Unless you change the app’s default settings, these notifications will also push to your phone, so you never have to wonder whether something is going on. That said, we understand that other issues may sometimes arise. 
                </div>
                <div class="qustion-ans">
                    If you run into a problem with your service, we want to hear about it right away. Log in to the app and contact us with all the details of what went wrong. Select from the following categories:
                </div>
                <div class="terms-box-digicts dash">
                    <div class="qustion-ans">
                        - Load unacceptably delayed
                    </div>
                    <div class="qustion-ans">
                        - Load never arrived
                    </div>
                    <div class="qustion-ans">
                       - Load damaged
                    </div>

                    <div class="qustion-ans">
                       - Load damaged
                    </div>

                    <div class="qustion-ans">
                       - Incorrect items in load
                    </div>

                    <div class="qustion-ans">
                       - Driver conduct issue
                    </div>

                    <div class="qustion-ans">
                       - Other
                    </div>
                </div>
               
                <div class="qustion-ans">
                   and let us know so that we can take appropriate action.
                </div>
                   
                    <div class="qustion-ans">
                    If there is <span>a technical problem with the app itself</span>, you’re in the right place! Follow these steps:
                    </div>
                    <div class="terms-box-digicts">
                        <div class="qustion-ans">
                           1. Close the app and open it again.
                        </div>

                        <div class="qustion-ans">
                           2. If the problem persists, check to make sure your phone has an internet connection.
                        </div>

                        <div class="qustion-ans">
                           3. Check in your phone’s App Store to make sure your app version is up to date. Update it if necessary, then try it again.
                        </div>

                        <div class="qustion-ans">
                           4. If the connection and app version are both fine, but a problem persists, turn your phone off, let it rest for a few seconds, and then turn it back on again. Once the phone is booted up, open the app again.
                        </div>

                        <div class="qustion-ans">
                           5. If all else fails, please use our <a target="_blank" href="{{url('contact_us')}}">online contact form</a> to let us know what’s going on.
                        </div>
                    </div>
            </div>

        @elseif(isset($page_slug) && $page_slug == 'user-signing-up')

            <div class="terms-box">
                
                <div class="qustion-title">
                    What are the requirements to sign up?
                </div>
                <div class="qustion-ans">
                    To sign up as a User, all we need is your basic contact information and a password to set up your account. If you are trying to sign up as a Driver, there are a few more steps. Please consult ((“What will I need to sign up and start driving?”))
                </div>

            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    I’m ready. Where do I sign up?
                </div>
                <div class="qustion-ans">
                    Visit our <a href="{{url('/')}}" target="_blank">home page</a> to sign up now!
                </div>
                
            </div>

        @elseif(isset($page_slug) && $page_slug == 'user-accessibility')

            <div class="terms-box">
                
                <div class="qustion-title">
                    What if I need help using the app or the site?
                </div>
                
                <div class="qustion-ans">
                    QuickPick recommends that people who have trouble interacting with our products use whatever assistance they need. Depending on your needs, resources such as translation or text-to-speech tools may be available for free online. If you have support workers who help you with day to day tasks, please speak with them about your options.
                </div>

                <div class="qustion-ans">
                    If you have general questions about the app, the site, or QuickPick in general, please feel free to call or <span class="underli">contact our Support team online</span> for clarification.
                </div>

            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    What if I have other accessibility concerns about QuickPick?
                </div>
                <div class="qustion-ans">
                    QuickPick endeavors in good faith to comply with all rules and regulations regarding the use of its service by disabled persons. 
                    If you are concerned that our site, app, or service may have unnecessary barriers to such use, please <span class="underli">contact us</span> and let us know 
                    right away! We want to be sure that QuickPick is available and easy to use for everyone who wants to use it.
                </div>
                
            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    ADA compliance statement
                </div>
                <div class="qustion-ans">
                    QuickPick, LLC makes every effort to comply with all relevant rules and regulations outlined in the Americans with Disabilities Act (“the ADA”), to the extent required by law. QuickPick, LLC does not discriminate in any hiring, training, disciplinary, or day-to-day work capacity based on disability or disabled status. In cases where reasonable workplace accommodations may be required for persons with disabilities, QuickPick, LLC will endeavor to make such accommodations, in accordance with the ADA and related federal laws and regulations.
                </div>
                
            </div>

        @elseif(isset($page_slug) && $page_slug == 'user-faq')

            <div class="terms-box">
                
                <div class="qustion-title">
                    What if my Driver is late/doesn’t show up?
                </div>
                
                <div class="qustion-ans">
                    Drivers who know they will be late have a responsibility to update the app with the nature and expected length of their delay. If a Driver is more than 30 minutes late without providing a reason in the app, or for a reason that seems suspect, please log in to the app and use our built-in feedback tool to let us know. (Select “Unacceptable Delay” as the reason for feedback.) We will review the delay closely and take such actions as we determine are necessary to prevent similar delays in the future.
                    <br><br>
                </div>

                <div class="qustion-ans">
                    If you suspect your Driver will not show up at all, again, tell us right away! We will reach out to the Driver and take swift action to rectify the problem.
                </div>

            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    What if there’s some other supply chain breakdown?
                </div>
                <div class="qustion-ans">
                    It is your Driver’s responsibility to inform you (via the app) of any issues regarding your load – for example, if the supplier will not release the 
                    load to the Driver, or the Driver only receives part of the load. If the problem lies with the supplier, it is the User’s responsibility to contact 
                    them and come to a satisfactory resolution.<br><br>
                </div>
                
                <div class="qustion-ans">
                    If a Driver arrives with a damaged, lost, incorrect, or partial load, and did not notify you ahead of time, please use the feedback tool in the app to let us know right away. We can then send the Driver back for a recovery run, or assign a different Driver to your load.
                </div>

            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    Will the Driver load/unload my delivery?
                </div>
                
                <div class="qustion-ans">
                    Not necessarily. Drivers are not contracted to perform cargo lumping (loading and unloading) duties; in addition, some Drivers may not have the physical capacity to help with such work. Therefore, it is QuickPick’s official policy to never require loading or unloading work from our Drivers. The responsibility will be on the supplier at one end, and you (the User) or your crew at the other.
                    <br><br>
                </div>
                
                <div class="qustion-ans">
                    With that being said, most of our Drivers are glad to pitch in if and when they can, so feel free to ask for a hand – just remember that we do not require them to say “yes.”
                </div>

            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    What if I need a lumper (loading and/or unloading assistant)?
                </div>
                
                <div class="qustion-ans">
                    According to the QuickPick <a href="{{url('/terms_and_conditions')}}" target="_blank">Terms and Conditions</a>, Users—not Drivers—are responsible for loading and unloading cargo. Users may request lumpers on a per-job basis by contacting QuickPick directly; there will be an extra cost to contract lumpers for a job. If you or your supplier will need lumpers to help load and/or unload cargo, it is your responsibility to let QuickPick know at the time you place the order. 
                </div>
                
            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    What if the Driver won’t deliver my load?
                </div>
                
                <div class="qustion-ans">
                    In most cases, this is the result of a load being unsuitable for the Driver’s vehicle. It is the Driver’s responsibility to notify you and QuickPick (via the app), so that you know what has happened, and we can assign a Driver with a vehicle more suited to the dimensions and/or weight of the load in question. This is why it is important to provide as accurate a size and weight estimate as possible when delivering a load with the QuickPick app.
                    <br><br>
                </div>
                
                <div class="qustion-ans">
                    In addition, we strictly disallow delivery of hazardous materials, live animals, and certain other goods via the QuickPick service. (See ((What kinds of loads can I have delivered?)) for the entire list.) If a Driver is asked to deliver a prohibited load, we expect them to refuse; in such a case, we will not be able to deliver the load for you.
                    <br><br>
                </div>

                <div class="qustion-ans">
                    In rare cases, a Driver may refuse to deliver a load despite it being non-hazardous and suited to their vehicle. In such an instance, we will assign a different Driver to your load right away, and take appropriate action(s) regarding the refusing Driver’s decision.
                    <br><br>
                </div>

            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    Can I have hazardous materials delivered with QuickPick?
                </div>
                
                <div class="qustion-ans">
                    No. Our terms expressly forbid the use of our service to transport such specialized goods. See ((What kinds of loads can I have delivered?)) for a more detailed explanation of our restrictions. Knowingly requesting delivery of a hazardous or otherwise-prohibited load through QuickPick is a violation of our Terms of Service.No. Our terms expressly forbid the use of our service to transport such specialized goods. See ((What kinds of loads can I have delivered?)) for a more detailed explanation of our restrictions. Knowingly requesting delivery of a hazardous or otherwise-prohibited load through QuickPick is a violation of our <a href="{{url('/terms_and_conditions')}}" target="_blank">Terms of Service</a>.
                </div>
                
            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    What if I get a rude or unprofessional Driver?
                </div>
                
                <div class="qustion-ans">
                    Please log in to the app after your interaction is complete, navigate to our feedback tool, and report a problem under the “Driver conduct issue” category. You can also leave the Driver a critical review, which will help warn other Users about your experience. (Please be clear about your bad experience in your review, but refrain from making personal attacks on the Driver if possible.)
                </div>
                
            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    What if there is an accident during delivery?
                </div>
                
                <div class="qustion-ans">
                    We advise our Drivers to prioritize their own health and safety above everything else in the event of an auto accident during delivery. Unfortunately, in some cases, this can mean severely delayed notifications for our Users. Once we know what has happened, you will receive an update in the app right away. If the load is intact, we will retrieve it and deliver it when we are able; otherwise, you may have to file a claim on your shipping insurance.
                </div>
                
                <div class="qustion-ans">
                    As usual, if a Driver is unexpectedly or unacceptably late, please use the feedback tool in the QuickPick app to let us know. If we later find that a traffic accident was the reason for the delay, we will provide you with an update at that time.
                </div>

            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    Is my cargo insured during delivery?
                </div>
                
                <div class="qustion-ans">
                    It is the User’s responsibility to purchase shipping insurance for each load handled by QuickPick. We strongly recommend you 
                    insure all such loads, for your own peace of mind. QuickPick does not provide insurance coverage for loads delivered with our service.
                </div>
                
            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    What if the Driver is injured while on site?
                </div>
                
                <div class="qustion-ans">
                    Drivers are not required to help Users load or unload their cargo, but many are glad to do so anyway. It is the Driver’s personal responsibility to stay safe during the course of their work. If they are injured as a result of lifting or carrying cargo, they assume responsibility for said injury. If they are injured for some other reason (i.e., falling debris), then normal worksite liability laws apply. Please ensure your Driver is wearing all required safety gear for your worksite before letting them help load or unload anything.
                </div>
                
            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    I have another question or issue. How do I get help?
                </div>
                
                <div class="qustion-ans">
                    QuickPick provides support staff to help answer your questions and resolve issues on a per-case basis. If you require our assistance, we can be 
                    reached by phone, by email, or through the <a href="{{url('/contact_us')}}" target="_blank">Contact Us</a> form on our website and app.
                </div>
                
            </div>

        @elseif(isset($page_slug) && $page_slug == 'driver-account-and-payment')

            <div class="terms-box">
                
                <div class="qustion-title">
                    How much does QuickPick pay?
                </div>
                
                <div class="qustion-ans">
                    We pride ourselves on offering a competitive per-delivery rate to Drivers who deliver with the QuickPick app. The rate changes depending on the size of the load, the size of your vehicle, and whether or not you are driving with a CDL (commercial driver’s license). In addition, there is a standard per-mile reimbursement for fuel that is not counted as a part of Driver pay.
                    <br><br>
                </div>

                <div class="qustion-ans">
                    Below you will find QuickPick’s basic, standard payment structure. Rates may change over time, based on factors including customer satisfaction and seasonality, so always check the app for your actual current rate.
                    <br><br>
                </div>
                
                <!--<div class="white-main-form table-white-section">
                        <div class="transactions-table table-responsive">
                            
                            <div class="table">
                                <div class="table-row heading">
                                    <div class="table-cell">No.</div>
                                    <div class="table-cell">Date</div>
                                    <div class="table-cell">Discriptipn</div>
                                    <div class="table-cell">Rate</div>
                                </div>                                                
                                <div class="table-row">
                                    <div class="table-cell">1</div>
                                    <div class="table-cell">20/06/2017</div>
                                    <div class="table-cell">easiest and most reliable payment method for the vast majority</div>
                                    <div class="table-cell rate">$120</div>
                                 </div>
                                 <div class="table-row">
                                    <div class="table-cell">2</div>
                                    <div class="table-cell">20/06/2017</div>
                                    <div class="table-cell">easiest and most reliable payment method for the vast majority</div>
                                    <div class="table-cell rate">$120</div>
                                 </div>
                                 <div class="table-row">
                                    <div class="table-cell">3</div>
                                    <div class="table-cell">20/06/2017</div>
                                    <div class="table-cell">easiest and most reliable payment method for the vast majority</div>
                                    <div class="table-cell rate">$120</div>
                                 </div>
                                 <div class="table-row">
                                    <div class="table-cell">4</div>
                                    <div class="table-cell">20/06/2017</div>
                                    <div class="table-cell">easiest and most reliable payment method for the vast majority</div>
                                    <div class="table-cell rate">$120</div>
                                 </div>
                                 <div class="table-row">
                                    <div class="table-cell">5</div>
                                    <div class="table-cell">20/06/2017</div>
                                    <div class="table-cell">easiest and most reliable payment method for the vast majority</div>
                                    <div class="table-cell rate">$120</div>
                                 </div>
                                <div class="clearfix"></div>
                            </div>
                           
                        </div>                        
                    </div>-->

                <div class="qustion-ans">
                    Again, remember that these rates may change. It is each Driver’s responsibility to check the app for their most accurate rate of pay.
                </div>

            </div>


            <div class="terms-box">
                
                <div class="qustion-title">
                    How often will I get paid?
                </div>
                
                <div class="qustion-ans">
                    QuickPick typically pays Drivers by ACH deposit. We find this is the easiest and most reliable payment method for the vast majority of our Drivers. In order to minimize fees associated with this type of payment, payment is made once every 14 days, starting from the date of your first delivery.  For simplicity’s sake, non-ACH payment methods (when used) are also limited to once every 14 days.
                </div>

            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    What payment options do I have?
                </div>
                
                <div class="qustion-ans">
                    QuickPick prefers to pay Drivers by direct bank account deposit, also known as ACH deposit or EFT. If you will require another method of payment, we may be able to accommodate you. Please contact us directly at the time you are contracted as a Driver to review what options are available.
                </div>

            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    What if there’s a payment problem?
                </div>
                
                <div class="qustion-ans">
                    If you have trouble receiving a payment, or you think a payment did not post to your bank account, please call or email our Support team right away. Remember, payments post once every 28 days at most.
                </div>

            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    What about tolls and similar fees?
                </div>
                
                <div class="qustion-ans">
                    QuickPick will reimburse normal toll road and parking fee costs for Drivers, as a part of their normal operating cost. Keep track of any such fees and enter them in the appropriate box when closing out a job. We will compare your total to our estimate (based on publicly available toll and fee rates, cross-referenced with your route); claims that seem exorbitant may be investigated more closely, so keep any receipts you are given.
                </div>

            </div>

        @elseif(isset($page_slug) && $page_slug == 'driver-accessibility')

            <div class="terms-box">
                
                <div class="qustion-title">
                    I have a disability. Can I still deliver with QuickPick?
                </div>
                
                <div class="qustion-ans">
                    Yes! In keeping with <span class="underli">the ruling of the Fifth Circuit Court of Appeals</span> in <span class="itali">Flynn vs. Distinctive Home Care, Inc.</span> (No. 15-50314, February 2016), QuickPick is glad to contract work to people who can meet our requirements on an ongoing basis, regardless of physical, emotional, developmental, or other disability. QuickPick will endeavor to make reasonable accommodations for Drivers with a disability who feel they need such accommodations. People with disabilities who can perform the work required of the position (with or without supports/accommodation) will be considered for contract work without prejudice.
                    <br><br>
                 </div>

                <div class="qustion-ans">
                    If you think you can perform the duties of the job, but you are not sure, please check our Work Requirements, and call or email us if you have any questions.
                </div>

            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    What if I need help using the app or site?
                </div>
                
                <div class="qustion-ans">
                    QuickPick recommends that people who have trouble interacting with our products use whatever assistance they need. If you believe that such assistance may be considered an accommodation for a disability as outlined in the Americans with Disabilities Act, please contact us directly to further discuss the matter before spending resources on it – we cannot guarantee assistance without first knowing the details of what will be required.
                </div>

                <div class="qustion-ans">
                    If you have general questions about the app, the site, or QuickPick in general, please feel free to call or e-mail our Support team for clarification.
                </div>

            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    What are the physical requirements of the work?
                </div>
                
                <div class="qustion-ans">
                    Delivering loads with QuickPick requires the following physical abilities:
                </div>
                <div class="terms-box-digicts">
                    <div class="qustion-ans">
                        1. The ability to safely operate a motor vehicle.
                    </div>

                    <div class="qustion-ans">
                        2. The ability to write legibly, and sign one’s name.
                    </div>

                    <div class="qustion-ans">
                        3. The ability to sit in a sedentary position for periods of up to four hours (while driving).
                    </div>
                </div>
                <div class="qustion-ans">
                    Per our <a href="{{url('terms_and_conditions')}}" target="_blank">Terms and Conditions</a>, Drivers are not required to stand, stoop, lift, turn, or otherwise interact with the loads they deliver. Loading and unloading cargo is the responsibility of the User, not the Driver.
                </div>
            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    ADA Compliance Statement
                </div>
                
                <div class="qustion-ans">
                    QuickPick, LLC makes every effort to comply with all relevant rules and regulations outlined in the Americans with Disabilities Act (“ADA”), to the extent required by law. QuickPick, LLC does not discriminate in any hiring, contracting, training, disciplinary, or day-to-day work capacity based on disability or disabled status. In cases where reasonable workplace accommodations may be required for persons with disabilities, QuickPick, LLC will endeavor to make such accommodations, in accordance with the ADA and related federal laws and regulations.
                </div>
            </div>

        @elseif(isset($page_slug) && $page_slug == 'driver-quick-pick-guide')
            
            <div class="terms-box">
                <div class="qustion-title">
                    What exactly does QuickPick do?
                </div>
                
                <div class="qustion-ans">
                    We contract with Drivers to serve the same-day needs of local businesses who find that they need additional materials to complete the day’s work (our “Users”). Our Drivers serve businesses ranging from flooring installers to florists and more. Our app makes it easy for such professionals (“Users”) to request fast delivery from a fleet of independent shipping agents (“Drivers”). As a Driver, you’ll use the app to acquire loads for delivery and get them to worksites in a timely manner.
                </div>
            </div>

            <div class="terms-box">
                
                <div class="qustion-title">
                    What are the minimum work requirements to make deliveries using the QuickPick app?
                </div>
                
                <div class="qustion-ans">
                    At a minimum, QuickPick requires the following from Drivers:
                </div>
                <div class="terms-box-digicts">
                    <div class="qustion-ans">
                        1. Previous driving experience with the vehicle(s) you intend to use for work
                    </div>

                    <div class="qustion-ans">
                        2. Previous driving experience in the Washington, D.C. metro area
                    </div>

                    <div class="qustion-ans">
                        3. Flexible availability, particularly during daytime hours when most of our clients are working
                    </div>

                    <div class="qustion-ans">
                        4. A friendly, courteous, and professional attitude
                    </div>

                    <div class="qustion-ans">
                        5. Valid vehicle registration and inspection, and adequate insurance for package delivery work
                    </div>

                    <div class="qustion-ans">
                        6. A valid U.S. driver’s license
                    </div>

                    <div class="qustion-ans">
                        7. A recent copy of your official DMV driving history (generated no more than 30 days before the date of your application)
                    </div>
                </div>
                <div class="qustion-ans">
                    In addition, if you would like to deliver using your cargo van, box truck, or flatbed, we will require:
                </div>
                <div class="terms-box-digicts">
                    <div class="qustion-ans">
                        1. Proof of your current CDL (commercial driver’s license)
                    </div>

                    <div class="qustion-ans">
                        2. A copy of your DOT (Department of Transportation) number
                    </div>
                </div>
                <div class="qustion-ans">
                    Each Driver is responsible for providing their own adequate insurance coverage. This responsibility is not QuickPick’s. In addition, QuickPick 
                    will <span>not</span> be held responsible or liable for any accident, delay, injury, or other mishap that occurs while engaged in QuickPick-related 
                    contract work. In plain English, this means we expect our Drivers to be responsible for their own safety and well-being while on 
                    the job.<span>(uber offers supplemental insurance we need to offer and see if we can monetize this)</span>
                </div>

            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    Is my vehicle eligible for delivering loads with QuickPick?
                </div>
                
                <div class="qustion-ans">
                    QuickPick expects Drivers to keep their work vehicle(s) in good working order at all times. If your vehicle is chronically unreliable, it may be a poor fit for our service. Of course, such a judgment call is outside of our purview, but we do collect as much official information on your vehicle’s quality and usefulness as we can reasonably acquire.
                </div>

                <div class="qustion-ans">
                    QuickPick requires the following from every Driver who signs up with us:
                </div>
                <div class="terms-box-digicts">
                    <div class="qustion-ans">
                        1. A current and valid driver’s license
                    </div>

                    <div class="qustion-ans">
                        2. A current DMV driving history (generated no more than 30 days before the date of your application)
                    </div>

                    <div class="qustion-ans">
                        3. Current and valid insurance for each vehicle you plan to use for QuickPick deliveries
                    </div>

                    <div class="terms-box-digicts two">
                        1. Note that delivering loads with the QuickPick app may require more insurance coverage than is normally required to operate a personal vehicle. Check with your insurer before applying to drive with us.
                    </div>

                    <div class="qustion-ans">
                        4. Current and valid vehicle registration in in the Driver’s name
                    </div>

                    <div class="qustion-ans">
                        5. Copy vehicle inspection report- where applicable.
                    </div>
                </div>
                <div class="qustion-ans">
                    If you and your vehicle can meet the above requirements, then you are eligible to be a Driver.
                </div>

            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    Is my standard auto insurance enough to drive with QuickPick?
                </div>
                
                <div class="qustion-ans">
                    It may or may not be. It is each Driver’s personal responsibility to check with their insurance agent and ensure that their coverage is adequate.
                </div>
            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    As a Driver, what steps will I follow for a load?
                </div>
                
                <div class="qustion-ans">
                    QuickPick specializes in same-day delivery of small and medium-sized loads to local businesses, typically providing them with supplemental materials they require for their own work. We have streamlined the process to be as simple as possible for our Drivers:
                </div>
                <div class="terms-box-digicts">
                    <div class="qustion-ans">
                        1. A load will be matched to the size of your work vehicle, and offered for delivery.
                    </div>

                    <div class="qustion-ans">
                        2. Once you accept the load, you will drive to the business that is supplying the load.
                    </div>

                    <div class="qustion-ans">
                        3. The load will be placed in your vehicle.(Use your judgement a small box will not be loaded into your vehicle)
                    </div>

                    <div class="qustion-ans">
                        4. The app will generate a suggested route. Using the route as your guide, you will drive the load from the supplier to the User’s worksite.
                    </div>

                    <div class="qustion-ans">
                        5. The User will unload the load from your vehicle, or curbside delivery will apply for smaller packages.
                    </div>

                    <div class="qustion-ans">
                        6. You will enter the app and mark the delivery as completed.
                    </div>
                </div>
                <div class="qustion-ans">
                    That’s all there is to it!
                </div>

            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    What is QuickPick’s delivery area?
                </div>
                
                <div class="qustion-ans">
                    QuickPick serves local businesses in the <span>D.C. metro area</span> with on-site deliveries. We do not currently serve any other markets. Please review the map below for our exact delivery area.
                    {{-- (delivery area map) --}}
                </div>
            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    As a Driver, do I need to provide a Bill of Lading?
                </div>
                
                <div class="qustion-ans">
                    Not always. You will confirm receipt of each load in the QuickPick app, often eliminating the need to issue a paper Bill of Lading. However, if a supplier or User requests that a Bill of Lading be filled out for their own records, you must do so. Suppliers will typically provide their own papers to fill out and sign in such cases.
                </div>
            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    What will Users expect from me as a Driver?
                </div>
                
                <div class="qustion-ans">
                    We warrant that all QuickPick Drivers will be friendly, professional in appearance and behavior, and timely in their work. Treat the work, and each User, with respect and courtesy, and you will find delivering with the QuickPick app both profitable and enjoyable.
                    <br><br>
                </div>

                <div class="qustion-ans">
                    At times, Users may request additional assistance from Drivers. Technically speaking, QuickPick Drivers are not required to help load or unload cargo from their vehicles, but in cases where it is feasible for you to do so, you may find that extending a bit of extra courtesy in that regard goes a long way towards keeping Users happy with your service. Use your best judgment, and do not engage in any activity which could endanger your health or safety. Remember, QuickPick is not liable for anything that happens to your vehicle or your person while you are working.
                </div>

            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    How do I use the app effectively?
                </div>
                
                <div class="qustion-ans">
                    The QuickPick app is designed to support Drivers at each step of the process. For a typical delivery run, you will interact with the app in the following ways:
                </div>
                <div class="terms-box-digicts">
                    <div class="qustion-ans">
                        1. Log in to the app and acquire a job.
                    </div>

                    <div class="qustion-ans">
                        2. Confirm receipt of your shipment from the supplier.
                    </div>

                    <div class="qustion-ans">
                        3. Acquire an efficient route to your destination.
                    </div>

                    <div class="qustion-ans">
                        4. Confirm arrival and offloading of the shipment to the User.
                    </div>

                    <div class="qustion-ans">
                        5. Close out the job.
                    </div>
                </div>
                <div class="qustion-ans">
                    In addition, you can check your job history, rate of pay, and accrued earnings in the app, as well as your average star rating (based on what our Users think of your work). If you need additional help, or have questions, you can even reach out to QuickPick’s support staff directly through the app.
                </div>

            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    What if there’s a problem?
                </div>
                
                <div class="qustion-ans">
                    If you run into a problem during the course of your QuickPick work, we are here to help. Start by reviewing the “Frequently Asked Questions and Concerns” section of our Help guide (online or in the app). If you are unable to find the answers you need, please call or contact our support staff.
                </div>
            </div>

        @elseif(isset($page_slug) && $page_slug == 'driver-faq')
            
            <div class="terms-box">
                <div class="qustion-title">
                    What types of loads will I ship?
                </div>
                
                <div class="qustion-ans">
                    As a Driver, you will ship most types of ordinary supplies our Users might need. These can include such items as flooring, siding, and/or surface finishing materials, basic construction supplies, and other non-perishable, non-hazardous items related to a User’s work. You will not be asked or expected to ship the following types of loads:
                </div>
                <div class="terms-box-digicts">
                    <div class="qustion-ans">
                        1. Hazardous materials, including dangerous chemicals, biological agents, and any other noxious, unstable, corrosive, or highly flammable items.
                    </div>

                    <div class="qustion-ans">
                        2. Items that require refrigeration or temperature control.
                    </div>

                    <div class="qustion-ans">
                        3. Exceptionally large loads of the type that will require a dump truck or other heavy construction vehicle.
                    </div>

                    <div class="qustion-ans">
                        4. People (i.e., taxi service).
                    </div>

                    <div class="qustion-ans">
                        5. Live animals.
                    </div>

                    <div class="qustion-ans">
                        6. Food and/or drink.
                    </div>
                </div>
                <div class="qustion-ans">
                    Users are responsible for ordering ahead to their suppliers, and for buying insurance on the loads they ship with QuickPick. Users are also responsible for unloading their cargo when it arrives on site. As a Driver, your responsibility is limited to verifying and accepting the load, and delivering it in a safe and timely manner.
                </div>

            </div>    

            <div class="terms-box">
                <div class="qustion-title">
                    What if I can’t accept a load?
                </div>
                
                <div class="qustion-ans">
                    QuickPick strives to match vehicles with loads of appropriate size, but there may be cases where a Driver is unable to accept a load from the supplier, due to unexpected size constraints or other issues. In such a case, please follow these steps:
                </div>
                <div class="terms-box-digicts">
                    <div class="qustion-ans">
                        1. Politely and clearly explain to the supplier why you cannot accept the load for delivery.
                    </div>

                    <div class="qustion-ans">
                        2. Log in to the app and use the incident report tool to notify us (and the User) immediately.
                    </div>

                    <div class="qustion-ans">
                        3. We will remove you from the assignment and assign a vehicle better suited to the load.
                    </div>
                </div>
                <div class="qustion-ans">
                    If you cannot accept a load for a reason other than weight or dimensional issues, politely try to reach a resolution with the supplier before notifying us. If the supplier remains unhelpful, we will review the problem and take action accordingly. For example, if the load is fragile and unsecured (i.e., loose plates of window glass), it is appropriate to ask the supplier to bind and secure the load so that it is reasonably unlikely to break during transit. If the supplier will not make this accommodation, we might suggest another supplier to the User, and update all involved parties as to the new time table. Each such issue will be handled on a per-case basis.
                </div>

            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    What if I get into an accident while driving?
                </div>
                
                <div class="qustion-ans">
                    QuickPick is not responsible in any way for anything that might happen to Drivers or their vehicles while they are making deliveries with the QuickPick app. It is the responsibility of each contracted Driver to procure and maintain adequate insurance coverage for the work they do, and to cultivate safe, defensive driving habits in order to minimize the risk of accident and/or injury while working.
                    <br><br>
                </div>

                <div class="qustion-ans">
                    Should an accident occur during the course of your work delivering a load acquired from the QuickPick app, please follow this procedure:
                </div>
                <div class="terms-box-digicts">
                    <div class="qustion-ans">
                        1. <span>Ensure your own health and safety first</span>. We cannot stress enough how important this is. Since you are responsible for your own well-being while on the road, we urge you to take whatever steps are necessary to secure your person, and, if possible, your vehicle from further accident, and confirm that you are still in good health. If you feel that you may have been injured, seek medical help immediately.
                    </div>

                    <div class="qustion-ans">
                        2. Once you are safe and sound, exchange insurance information with any other drivers involved, and contact your insurance company to report the accident.
                    </div>

                    <div class="qustion-ans">
                        3. After reporting the accident to your insurance company, please log in to the app and report that you will be unable to complete your delivery (or that you will be late, in the case of a minor accident that does not impact your vehicle’s road performance). Use the space provided to explain that an accident has occurred. You can also call us, if you prefer.
                    </div>

                    <div class="qustion-ans">
                        4. QuickPick will work to recover the load, if possible, and reassign the job to another Driver, or relay an appropriate update to the User in the case of a minor accident that will only cause a temporary delay.
                    </div>
                </div>
                <div class="qustion-ans">
                    <span class="underli">This article</span> (and accompanying <span class="underli">video</span>), published by Automotive Fleet Magazine and provided by Allstate Insurance, offers some good pointers if you would like a quick refresher course on driving defensively while on the job.
                </div>

            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    What if I get pulled over while I’m on a delivery?
                </div>
                
                <div class="qustion-ans">
                    QuickPick is not responsible in any way for anything that might happen to its Drivers while they are making deliveries using the QuickPick app. This includes being pulled over and/or arrested. It is the responsibility of each contracted Driver to obey all traffic laws and drive safely at all times.
                </div>

                <div class="qustion-ans">
                    If you are pulled over, it is generally recommended that you take the following steps:
                </div>
                <div class="terms-box-digicts">
                    <div class="qustion-ans">
                        1. Turn off the vehicle, roll down the driver’s side window, and remain inside with your hands on the steering wheel.
                    </div>

                    <div class="qustion-ans">
                        2. Wait quietly for the police officer to approach.
                    </div>

                    <div class="qustion-ans">
                        3. When interacting with an officer of the law, speak calmly and politely, and describe what you are going to do before you do it. For example, if an officer asks for your driver’s license, and you keep it in a back pocket, tell them you are going to reach into your back pocket to retrieve it.
                    </div>

                    <div class="qustion-ans">
                        4. Be patient. The QuickPick app has built-in tools to report when there will be a delay. You can use them after the traffic stop is done.
                    </div>
                </div>
                <div class="qustion-ans">
                    When the traffic stop is finished, if there will be a delay or cancellation as a result of the stop, please log in to the QuickPick app and report that you will be delayed (or, if arrested, that you will be unable to complete the load). Use the space provided to explain what has happened. We will notify the User in case of a delay, or reassign the load in case of a cancellation. If you are unable to log in to the app on your phone, you should call us when you can instead.
                </div>

                <div class="qustion-ans">
                    Disclaimer: The above should not be construed as legal advice in any capacity, nor should QuickPick, LLC, be construed as a knowledgeable or  authoritative source for legal advice on this or any other subject. If you have legal questions or concerns of any kind, consult your attorney.
                </div>

            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    What if I need a lumper (loading and/or unloading assistant)?
                </div>
                
                <div class="qustion-ans">
                    According to the QuickPick <a href="{{url('terms_and_conditions')}}" target="_blank">Terms and Conditions</a>, Users—not Drivers—are responsible for loading and unloading cargo; however, it is possible that such service may be specifically requested by a User. If a User will need lumpers to help them load and/or unload cargo, it is the responsibility of that User to let QuickPick know beforehand. If that responsibility is not met, and an impasse results, please call us immediately and explain the situation. We will connect you with lumpers for the duration of the job, and charge the cost to the User in question.
                </div>
            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    Will I need to lift heavy objects?
                </div>
                
                <div class="qustion-ans">
                    No; as outlined at the bottom of our <a href="{{url('terms_and_conditions')}}" target="_blank">Terms and Conditions</a>, loading and unloading are the responsibility of our Users, not our Drivers. As a Driver, you are welcome to help load or unload any delivery if you want, but you are never under any obligation to do so. If a supplier or User insists that you load or unload cargo yourself, call and let us know right away so that we can help resolve the issue.
                </div>
            </div>
            
            <div class="terms-box">
                <div class="qustion-title">
                    What if I injure myself while loading or unloading a shipment?
                </div>
                
                <div class="qustion-ans">
                    It is QuickPick’s sincere hope that this never happens. We typically require Users to load and unload their own cargo, as outlined in 
                    our <a href="{{url('terms_and_conditions')}}" target="_blank">Terms and Conditions</a>. However, if you do agree to help, and you are then injured, QuickPick highly recommends you close the job and seek medical attention immediately. QuickPick is not responsible and cannot be held liable in any way for the well-being of Drivers while they are at work, so we expect Drivers to take whatever steps they consider reasonable and prudent to stay safe and in good health.
                </div>
            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    Will I ever need to handle hazardous materials?
                </div>
                
                <div class="qustion-ans">
                    No, you will not. Our terms expressly forbid the use of our service to transport such specialized goods. If you arrive at a shipment and discover that it is hazardous, close the job and report it to us in the app.
                </div>
            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    Why am I not receiving any delivery requests?
                </div>
                
                <div class="qustion-ans">
                    This may happen for any of several reasons. To start with, try this quick troubleshooting guide:
                </div>
                <div class="terms-box-digicts">
                    <div class="qustion-ans">
                        1. Make sure your Location data is on, and the QuickPick app is up to date.
                    </div>

                    <div class="qustion-ans">
                        2. Make sure your app settings don’t unnecessarily limit the deliveries you see.
                    </div>

                    <div class="qustion-ans">
                        3. Make sure you don’t accidentally have a job currently open.
                    </div>
                </div>
                <div class="qustion-ans">
                    If none of these is the culprit, it could be that order volume for deliveries is low at the moment. (Since we serve businesses with on-site deliveries during their normal hours of operation, daytime and weekday hours are typically busier than other times.) If the problem seems to persist, please let us know using the Contact Us tool in your app.
                </div>

            </div>

            <div class="terms-box">
                <div class="qustion-title">
                    What if the app shows incorrect mileage for a job?
                </div>
                
                <div class="qustion-ans">
                    It is possible that, due to phone or app issues, a job’s mileage may be calculated incorrectly. If you believe this is the case for one of your jobs, let us know in the Feedback tool of the app. We will review your request based on the suggested route for your pickup and drop off locations, so if there is extra information we need to know (for example, if a road was temporarily blocked or you had to take a detour), please be sure to mention it when submitting your request.
                </div>

            </div>


            <div class="terms-box">
                <div class="qustion-title">
                    I have another question or issue. How do I get help?
                </div>
                
                <div class="qustion-ans">
                    QuickPick provides support staff to help answer your questions and resolve issues on a per-case basis. If you require our assistance, we can be reached by phone, by email, or through the contact form on our website and app.
                </div>

            </div>


        @endif

   
    
  
   
    </div>
</div>


@stop