import { boot } from 'quasar/wrappers';
import { createI18n } from 'vue-i18n';

import messages from 'src/i18n';

type MessageSchema = typeof messages.enUS;

export default boot(({ app }) => {
  const i18n = createI18n<[MessageSchema], 'en-US', 'sr'>({
    // legacy: false,
    locale: 'sr',
    fallbackLocale: 'en_US',
    globalInjection: true,
    messages,
  });

  // Set i18n instance on app
  app.use(i18n);
});
