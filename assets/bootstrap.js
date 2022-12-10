import { startStimulusApp } from '@symfony/stimulus-bridge';
import LoginComponent from '../src/Twig/Components/User/Login/LoginComponent_controller';
import SignupComponent from '../src/Twig/Components/User/Signup/SignupComponent_controller';
import ProfileComponent from '../src/Twig/Components/User/Profile/ProfileComponent_controller';
import AlertComponent from '../src/Twig/Components/Alert/AlertComponent_controller';
import PasswordRememberComponent from '../src/Twig/Components/User/PasswordRemember/PasswordRememberComponent_controller';

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));

// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
app.register('LoginComponent', LoginComponent);
app.register('SignupComponent', SignupComponent);
app.register('ProfileComponent', ProfileComponent);
app.register('AlertComponent', AlertComponent);
app.register('PasswordRememberComponent', PasswordRememberComponent);