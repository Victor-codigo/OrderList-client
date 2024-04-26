import { startStimulusApp } from '@symfony/stimulus-bridge';

// USER
import LoginComponent from 'App/Twig/Components/User/Login/LoginComponent_controller';
import SignupComponent from 'App/Twig/Components/User/Signup/SignupComponent_controller';
import ProfileComponent from 'App/Twig/Components/User/Profile/ProfileComponent_controller';
import PasswordRememberComponent from 'App/Twig/Components/User/PasswordRemember/PasswordRememberComponent_controller';
import PasswordChangeComponent from 'App/Twig/Components/User/PasswordChange/PasswordChangeComponent_controller';
import EmailChangeComponent from 'App/Twig/Components/User/EmailChange/EmailChangeComponent_controller';
import UserRemoveComponent from 'App/Twig/Components/User/UserRemove/UserRemoveComponent_controller';

// GENERAL
import AlertComponent from 'App/Twig/Components/Alert/AlertComponent_controller';
import NavigationBarComponent from 'App/Twig/Components/NavigationBar/NavigationBar_controller';
import ModalComponent from 'App/Twig/Components/Modal/Modal_Component';
import DropZoneComponent from 'App/Twig/Components/Controls/DropZone/DropZone_Component';
import ImageAvatarComponent from 'App/Twig/Components/Controls/ImageAvatar/ImageAvatar_Component';
import ItemPriceAddComponent from 'App/Twig/Components/Controls/ItemPriceAdd/ItemPriceAdd_controller';
import ButtonLoadingComponent from 'App/Twig/Components/Controls/ButtonLoading/ButtonLoading_controller';
import ItemsListAjaxComponent from 'App/Twig/Components/HomeSection/ItemsListAjax/ItemsListAjaxComponent_controller';

import PaginatorComponent from 'App/Twig/Components/Paginator/Paginator_Component';
import PaginatorJsComponent from 'App/Twig/Components/PaginatorJs/PaginatorJs_Component';
import ListComponent from 'App/Twig/Components/List/List_controller';
import SearchBarComponent from 'App/Twig/Components/HomeSection/SearchBar/SearchBar_controller';
import ContentLoaderJsComponent from 'App/Twig/Components/Controls/ContentLoaderJs/ContentLoaderJsComponent_controller';

// HOME LIST
import HomeSectionComponent from 'App/Twig/Components/HomeSection/Home/HomeSection_controller';
import HomeListComponent from 'App/Twig/Components/HomeSection/HomeList/List/HomeList_controller';
import HomeListItemComponent from 'App/Twig/Components/HomeSection/HomeList/ListItem/HomeListItem_controller';


// GROUP
import GroupHomeComponent from 'App/Twig/Components/Group/GroupHome/Home/GroupHomeSection_controller';
import GroupListItemComponent from 'App/Twig/Components/Group/GroupHome/ListItem/GroupListItem_controller';
import GroupCreateComponent from 'App/Twig/Components/Group/GroupCreate/GroupCreate_controller';
import GroupModifyComponent from 'App/Twig/Components/Group/GroupModify/GroupModify_controller';
import GroupRemoveComponent from 'App/Twig/Components/Group/GroupRemove/GroupRemoveComponent_controller';
import GroupListComponent from 'App/Twig/Components/Group/GroupList/List/GroupList_controller';
import GroupUsersListComponent from 'App/Twig/Components/Group/GroupUsersList/List/GroupUsersList_controller';
import GroupUsersListItemComponent from 'App/Twig/Components/Group/GroupUsersList/ListItem/GroupUsersListItem_controller';
import GroupUserRemoveComponent from 'App/Twig/Components/Group/GroupUserRemove/GroupUserRemove_controller';
import GroupUserAddComponent from 'App/Twig/Components/Group/GroupUserAdd/GroupUserAdd_controller';

// LIST ORDERS
import ListOrdersHomeSectionComponent from 'App/Twig/Components/ListOrders/ListOrdersHome/Home/ListOrdersHomeSection_controller';
import ListOrdersListItemComponent from 'App/Twig/Components/ListOrders/ListOrdersHome/ListItem/ListOrdersListItem_controller';
import ListOrdersCreateComponent from 'App/Twig/Components/ListOrders/ListOrdersCreate/ListOrdersCreate_controller';
import ListOrdersCreateFromComponent from 'App/Twig/Components/ListOrders/ListOrdersCreateFrom/ListOrdersCreateFrom_controller';
import ListOrdersInfoComponent from 'App/Twig/Components/ListOrders/ListOrdersInfo/ListOrdersInfo_controller';
import ListOrdersModifyComponent from 'App/Twig/Components/ListOrders/ListOrdersModify/ListOrdersModify_controller';
import ListOrdersRemoveComponent from 'App/Twig/Components/ListOrders/ListOrdersRemove/ListOrdersRemoveComponent_controller';
import ListOrdersListAjaxComponent from 'App/Twig/Components/ListOrders/ListOrdersListAjax/ListOrdersListAjaxComponent_controller';

// ORDERS
import OrderHomeSectionComponent from 'App/Twig/Components/Order/OrderHome/Home/OrderHomeSection_controller';
import OrderListItemComponent from 'App/Twig/Components/Order/OrderHome/ListItem/OrderListItem_controller';
import OrderCreateComponent from 'App/Twig/Components/Order/OrderCreate/OrderCreate_controller';
import OrderProductAndShopComponent from 'App/Twig/Components/Controls/OrderProductAndShop/OrderProductAndShop_controller';
import OrderProductsListAjaxComponent from 'App/Twig/Components/Order/OrderProductsListAjax/OrderProductsListAjaxComponent_controller';
import OrderModifyComponent from 'App/Twig/Components/Order/OrderModify/OrderModify_controller';
import OrderRemoveComponent from 'App/Twig/Components/Order/OrderRemove/OrderRemoveComponent_controller';
import OrderInfoComponent from 'App/Twig/Components/Order/OrderInfo/OrderInfo_controller';

// SHOP
import ShopHomeSectionComponent from 'App/Twig/Components/Shop/ShopHome/Home/ShopHomeSection_controller';
import ShopCreateComponent from 'App/Twig/Components/Shop/ShopCreate/ShopCreate_controller';
import ShopCreateAjaxComponent from 'App/Twig/Components/Shop/ShopCreateAjax/ShopCreateAjax_controller';
import ShopModifyComponent from 'App/Twig/Components/Shop/ShopModify/ShopModify_controller';
import ShopRemoveComponent from 'App/Twig/Components/Shop/ShopRemove/ShopRemoveComponent_controller';
import PaginatorContentLoaderJsComponent from 'App/Twig/Components/Controls/PaginatorContentLoaderJs/PaginatorContentLoaderJsComponent_controller';
import ShopListItemComponent from 'App/Twig/Components/Shop/ShopHome/ListItem/ShopListItem_controller';
import ShopsListAjaxComponent from 'App/Twig/Components/Shop/ShopsListAjax/ShopsListAjaxComponent_controller';
import ShopInfoComponent from 'App/Twig/Components/Shop/ShopInfo/ShopInfo_controller';


// PRODUCT
import ProductHomeSectionComponent from 'App/Twig/Components/Product/ProductHome/Home/ProductHomeSection_controller';
import ProductCreateComponent from 'App/Twig/Components/Product/ProductCreate/ProductCreate_controller';
import ProductCreateAjaxComponent from 'App/Twig/Components/Product/ProductCreateAjax/ProductCreateAjax_controller';
import ProductModifyComponent from 'App/Twig/Components/Product/ProductModify/ProductModify_controller';
import ProductRemoveComponent from 'App/Twig/Components/Product/ProductRemove/ProductRemoveComponent_controller';
import ProductListItemComponent from 'App/Twig/Components/Product/ProductHome/ListItem/ProductListItem_controller';
import ProductInfoComponent from 'App/Twig/Components/Product/ProductInfo/ProductInfo_controller';
import ProductsListAjaxComponent from 'App/Twig/Components/Product/ProductsListAjax/ProductsListAjaxComponent_controller';


// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));

// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);

// GENERAL
app.register('AlertComponent', AlertComponent);
app.register('NavigationBarComponent', NavigationBarComponent);
app.register('ModalComponent', ModalComponent);
app.register('DropZoneComponent', DropZoneComponent);
app.register('ImageAvatarComponent', ImageAvatarComponent);
app.register('ItemPriceAddComponent', ItemPriceAddComponent);
app.register('ButtonLoadingComponent', ButtonLoadingComponent);
app.register('PaginatorComponent', PaginatorComponent);
app.register('PaginatorJsComponent', PaginatorJsComponent);
app.register('ListComponent', ListComponent);
app.register('SearchBarComponent', SearchBarComponent);
app.register('ContentLoaderJsComponent', ContentLoaderJsComponent);
app.register('ItemsListAjaxComponent', ItemsListAjaxComponent);

// HOME LIST
app.register('HomeSectionComponent', HomeSectionComponent);
app.register('HomeListComponent', HomeListComponent);
app.register('HomeListItemComponent', HomeListItemComponent);


// USER
app.register('LoginComponent', LoginComponent);
app.register('SignupComponent', SignupComponent);
app.register('ProfileComponent', ProfileComponent);
app.register('PasswordRememberComponent', PasswordRememberComponent);
app.register('PasswordChangeComponent', PasswordChangeComponent);
app.register('EmailChangeComponent', EmailChangeComponent);
app.register('UserRemoveComponent', UserRemoveComponent);

// GROUP
app.register('GroupHomeComponent', GroupHomeComponent);
app.register('GroupListItemComponent', GroupListItemComponent);
app.register('GroupCreateComponent', GroupCreateComponent);
app.register('GroupModifyComponent', GroupModifyComponent);
app.register('GroupRemoveComponent', GroupRemoveComponent);
app.register('GroupListComponent', GroupListComponent);
app.register('GroupUsersListComponent', GroupUsersListComponent);
app.register('GroupUsersListItemComponent', GroupUsersListItemComponent);
app.register('GroupUserRemoveComponent', GroupUserRemoveComponent);
app.register('GroupUserAddComponent', GroupUserAddComponent);

// LIST ORDERS
app.register('ListOrdersHomeSectionComponent', ListOrdersHomeSectionComponent);
app.register('ListOrdersListItemComponent', ListOrdersListItemComponent);
app.register('ListOrdersCreateComponent', ListOrdersCreateComponent);
app.register('ListOrdersCreateFromComponent', ListOrdersCreateFromComponent);
app.register('ListOrdersInfoComponent', ListOrdersInfoComponent);
app.register('ListOrdersModifyComponent', ListOrdersModifyComponent);
app.register('ListOrdersRemoveComponent', ListOrdersRemoveComponent);
app.register('ListOrdersListAjaxComponent', ListOrdersListAjaxComponent);

// ORDERS
app.register('OrderHomeSectionComponent', OrderHomeSectionComponent);
app.register('OrderListItemComponent', OrderListItemComponent);
app.register('OrderCreateComponent', OrderCreateComponent);
app.register('OrderProductAndShopComponent', OrderProductAndShopComponent);
app.register('OrderProductsListAjaxComponent', OrderProductsListAjaxComponent);
app.register('OrderModifyComponent', OrderModifyComponent);
app.register('OrderRemoveComponent', OrderRemoveComponent);
app.register('OrderInfoComponent', OrderInfoComponent);

// SHOP
app.register('ShopHomeSectionComponent', ShopHomeSectionComponent);
app.register('ShopCreateComponent', ShopCreateComponent);
app.register('ShopCreateAjaxComponent', ShopCreateAjaxComponent);
app.register('ShopModifyComponent', ShopModifyComponent);
app.register('ShopRemoveComponent', ShopRemoveComponent);
app.register('ShopListItemComponent', ShopListItemComponent);
app.register('PaginatorContentLoaderJsComponent', PaginatorContentLoaderJsComponent);
app.register('ShopsListAjaxComponent', ShopsListAjaxComponent);
app.register('ShopInfoComponent', ShopInfoComponent);


// PRODUCT
app.register('ProductHomeSectionComponent', ProductHomeSectionComponent);
app.register('ProductCreateComponent', ProductCreateComponent);
app.register('ProductCreateAjaxComponent', ProductCreateAjaxComponent);
app.register('ProductModifyComponent', ProductModifyComponent);
app.register('ProductRemoveComponent', ProductRemoveComponent);
app.register('ProductListItemComponent', ProductListItemComponent);
app.register('ProductInfoComponent', ProductInfoComponent);
app.register('ProductsListAjaxComponent', ProductsListAjaxComponent);
