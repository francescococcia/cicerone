$.validator.addMethod(
  "maxfilesize",
  function (value, element) {
      if (this.optional(element) || ! element.files || ! element.files[0]) {
          return true;
      } else {
          return element.files[0].size <= 1024 * 1024 * 2;
      }
  },
  'The file size can not exceed 2MB.'
);
