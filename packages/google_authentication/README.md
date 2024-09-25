# Network International payments for Community Store Concrete CMS

Network International Checkout payment add-on for Community Store for Concrete CMS


## Setup
Install Community Store First.

Code for custom redirect to payment gateway

Include the Controller:
````
use Concrete\Package\CommunityStoreNetworkInternational\Src\CommunityStore\Payment\Methods\CommunityStoreNetworkInternational\CommunityStoreNetworkInternationalPaymentMethod as PaymentMethodNI;
````

Place the below code in: Concrete\Package\CommunityStore\Controller\SinglePage\Checkout::external
````
if($pm->getMethodController() instanceof PaymentMethodNI){
    return \Concrete\Core\Routing\Redirect::to($pm->getMethodController()->getAction());
}

````


