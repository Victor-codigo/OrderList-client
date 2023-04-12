import { startStimulusApp } from '@symfony/stimulus-bridge';

// USER
import LoginComponent from '../src/Twig/Components/User/Login/LoginComponent_controller';
import SignupComponent from '../src/Twig/Components/User/Signup/SignupComponent_controller';
import ProfileComponent from '../src/Twig/Components/User/Profile/ProfileComponent_controller';
import PasswordRememberComponent from '../src/Twig/Components/User/PasswordRemember/PasswordRememberComponent_controller';
import PasswordChangeComponent from '../src/Twig/Components/User/PasswordChange/PasswordChangeComponent_controller';
import EmailChangeComponent from '../src/Twig/Components/User/EmailChange/EmailChangeComponent_controller';
import UserRemoveComponent from '../src/Twig/Components/User/UserRemove/UserRemoveComponent_controller';

// GENERAL
import AlertComponent from '../src/Twig/Components/Alert/AlertComponent_controller';
import NavigationBar from '../src/Twig/Components/NavigationBar/NavigationBar_controller';
import ModalComponent from '../src/Twig/Components/Modal/Modal_Component';
import DropZoneComponent from '../src/Twig/Components/Controls/DropZone/DropZone_Component';
import ImageAvatarComponent from '../src/Twig/Components/Controls/ImageAvatar/ImageAvatar_Component';
import PaginatorComponent from '../src/Twig/Components/Paginator/Paginator_Component';
import ListComponent from '../src/Twig/Components/List/List_controller';

// GROUP
import GroupCreateComponent from '../src/Twig/Components/Group/GroupCreate/GroupCreate_controller';
import GroupModifyComponent from '../src/Twig/Components/Group/GroupModify/GroupModify_controller';
import GroupRemoveComponent from '../src/Twig/Components/Group/GroupRemove/GroupRemove_controller';
import GroupListComponent from '../src/Twig/Components/Group/GroupList/List/GroupList_controller';
import GroupListItemComponent from '../src/Twig/Components/Group/GroupList/ListItem/GroupListItem_controller';

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
app.register('PaginatorComponent', PaginatorComponent);
app.register('ListComponent', ListComponent);

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