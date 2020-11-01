# Styling

By default, this module loads the original klaro styles. If you want to apply
custom styles, you can advise silverstripe to not load klaro by adding:

```yaml
---
Name: my-silverstripe-klaro
After: silverstripe-klaro
---
SilverStripe\CMS\Controllers\ContentController:
  load_klaro_css: false
```

You can now include any other css styles you want or build the klaro style
from scratch using sass.
