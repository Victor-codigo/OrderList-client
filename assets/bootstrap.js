import { startStimulusApp } from '@symfony/stimulus-bridge';

// USER
import LoginComponent from '/src/Twig/Components/User/Login/LoginComponent_controller';
import SignupComponent from '/src/Twig/Components/User/Signup/SignupComponent_controller';
import ProfileComponent from '/src/Twig/Components/User/Profile/ProfileComponent_controller';
import PasswordRememberComponent from '/src/Twig/Components/User/PasswordRemember/PasswordRememberComponent_controller';
import PasswordChangeComponent from '/src/Twig/Components/User/PasswordChange/PasswordChangeComponent_controller';
import EmailChangeComponent from '/src/Twig/Components/User/EmailChange/EmailChangeComponent_controller';
import UserRemoveComponent from '/src/Twig/Components/User/UserRemove/UserRemoveComponent_controller';

// GENERAL
import AlertComponent from '/src/Twig/Components/Alert/AlertComponent_controller';
import NavigationBar from '/src/Twig/Components/NavigationBar/NavigationBar_controller';
import ModalComponent from '/src/Twig/Components/Modal/Modal_Component';
import DropZoneComponent from '/src/Twig/Components/Controls/DropZone/DropZone_Component';
import ImageAvatarComponent from '/src/Twig/Components/Controls/ImageAvatar/ImageAvatar_Component';
import ItemPriceAddComponent from '/src/Twig/Components/Controls/ItemPriceAdd/ItemPriceAdd_controller';
// import ListItemsComponent from '/src/Twig/Components/Controls/ListItems/ListItems_controller';
// import ListItemComponent from '/src/Twig/Components/Controls/ListItems/Item/Item_controller';
import PaginatorComponent from '/src/Twig/Components/Paginator/Paginator_Component';
import PaginatorJsComponent from '/src/Twig/Components/PaginatorJs/PaginatorJs_Component';
import ListComponent from '/src/Twig/Components/List/List_controller';
import SearchBarComponent from '/src/Twig/Components/SearchBar/SearchBar_controller';
import ContentLoaderJsComponent from '/src/Twig/Components/Controls/ContentLoaderJs/ContentLoaderJsComponent_controller';

// HOME LIST
import HomeSectionComponent from '/src/Twig/Components/HomeSection/Home/HomeSection_controller';
import HomeListComponent from '/src/Twig/Components/HomeSection/HomeList/List/HomeList_controller';
import HomeListItemComponent from '/src/Twig/Components/HomeSection/HomeList/ListItem/HomeListItem_controller';

// GROUP
import GroupCreateComponent from '/src/Twig/Components/Group/GroupCreate/GroupCreate_controller';
import GroupModifyComponent from '/src/Twig/Components/Group/GroupModify/GroupModify_controller';
import GroupRemoveComponent from '/src/Twig/Components/Group/GroupRemove/GroupRemove_controller';
import GroupListComponent from '/src/Twig/Components/Group/GroupList/List/GroupList_controller';
import GroupListItemComponent from '/src/Twig/Components/Group/GroupList/ListItem/GroupListItem_controller';
import GroupUsersListComponent from '/src/Twig/Components/Group/GroupUsersList/List/GroupUsersList_controller';
import GroupUsersListItemComponent from '/src/Twig/Components/Group/GroupUsersList/ListItem/GroupUsersListItem_controller';
import GroupUserRemoveComponent from '/src/Twig/Components/Group/GroupUserRemove/GroupUserRemove_controller';
import GroupUserAddComponent from '/src/Twig/Components/Group/GroupUserAdd/GroupUserAdd_controller';

// ORDERS
// import OrdersListComponent from '/src/Twig/Components/Orders/OrdersList/List/OrdersList_controller';
// import OrdersListItemComponent from '/src/Twig/Components/Orders/OrdersList/ListItem/OrdersListItem_controller';

// SHOP
import ShopCreateComponent from '/src/Twig/Components/Shop/ShopCreate/ShopCreate_controller';
import ShopModifyComponent from '/src/Twig/Components/Shop/ShopModify/ShopModify_controller';
import ShopRemoveComponent from '/src/Twig/Components/Shop/ShopRemove/ShopRemoveComponent_controller';
import PaginatorContentLoaderJsComponent from '/src/Twig/Components/Controls/PaginatorContentLoaderJs/PaginatorContentLoaderJsComponent_controller';
import ShopListItemComponent from '/src/Twig/Components/Shop/ShopHome/ListItem/ShopListItem_controller';
import ShopsListAjaxComponent from '/src/Twig/Components/Shop/ShopsListAjax/ShopsListAjaxComponent_controller';

// PRODUCT
import ProductHomeSectionComponent from '/src/Twig/Components/Product/ProductHome/Home/ProductHomeSection_controller';
import ProductCreateComponent from '/src/Twig/Components/Product/ProductCreate/ProductCreate_controller';
import ProductModifyComponent from '/src/Twig/Components/Product/ProductModify/ProductModify_controller';
import ProductRemoveComponent from '/src/Twig/Components/Product/ProductRemove/ProductRemoveComponent_controller';
import ProductListItemComponent from '/src/Twig/Components/Product/ProductHome/ListItem/ProductListItem_controller';




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
app.register('NavigationBar', NavigationBar);
app.register('ModalComponent', ModalComponent);
app.register('DropZoneComponent', DropZoneComponent);
app.register('ImageAvatarComponent', ImageAvatarComponent);
app.register('ItemPriceAddComponent', ItemPriceAddComponent);
// app.register('ListItemsComponent', ListItemsComponent);
// app.register('ListItemComponent', ListItemComponent);
app.register('PaginatorComponent', PaginatorComponent);
app.register('PaginatorJsComponent', PaginatorJsComponent);
app.register('ListComponent', ListComponent);
app.register('SearchBarComponent', SearchBarComponent);
app.register('ContentLoaderJsComponent', ContentLoaderJsComponent);

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
app.register('GroupCreateComponent', GroupCreateComponent);
app.register('GroupModifyComponent', GroupModifyComponent);
app.register('GroupRemoveComponent', GroupRemoveComponent);
app.register('GroupListComponent', GroupListComponent);
app.register('GroupListItemComponent', GroupListItemComponent);
app.register('GroupUsersListComponent', GroupUsersListComponent);
app.register('GroupUsersListItemComponent', GroupUsersListItemComponent);
app.register('GroupUserRemoveComponent', GroupUserRemoveComponent);
app.register('GroupUserAddComponent', GroupUserAddComponent);

// ORDERS
// app.register('OrdersListComponent', OrdersListComponent);
// app.register('OrdersListItemComponent', OrdersListItemComponent);


// SHOP
app.register('ShopCreateComponent', ShopCreateComponent);
app.register('ShopModifyComponent', ShopModifyComponent);
app.register('ShopRemoveComponent', ShopRemoveComponent);
app.register('ShopListItemComponent', ShopListItemComponent);
app.register('PaginatorContentLoaderJsComponent', PaginatorContentLoaderJsComponent);
app.register('ShopsListAjaxComponent', ShopsListAjaxComponent);


// PRODUCT
app.register('ProductHomeSectionComponent', ProductHomeSectionComponent);
app.register('ProductCreateComponent', ProductCreateComponent);
app.register('ProductModifyComponent', ProductModifyComponent);
app.register('ProductRemoveComponent', ProductRemoveComponent);
app.register('ProductListItemComponent', ProductListItemComponent);

