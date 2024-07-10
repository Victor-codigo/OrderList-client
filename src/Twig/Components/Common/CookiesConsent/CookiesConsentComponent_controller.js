import { Controller } from '@hotwired/stimulus';
import * as CookiesConsent from 'App/modules/CookieConsent';

export default class extends Controller {
    connect() {
        CookiesConsent.load();
    }
}
