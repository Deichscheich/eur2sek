// jQuery(document).ready(function ($) {
//         $('#e2s-convert').click(function (e) {
//             e.preventDefault();
//             // let formData = $('textarea[name="e2s-input-textarea"]').serializeArray();
//             let formData = $('form#e2s-input').serializeArray();

//             $.ajax({
//                 method : 'POST',
//                 dataType : 'json',
//                 url: e2s.ajax_url,
//                 data: { 
//                   action: 'e2s_logic',
//                   nonce: e2s.nonce,
//                   e2s_input: formData
//                 //   e2s_input: $('textarea[name="e2s-input-textarea"]').val()
//                 },
//                 success: function (response) {
//                      if(response.type == 'success') {
//                       console.log('success');
//                         console.log(response);
//                      }else{
//                          console.log("THERE WAS A PROBLEM");
//                      }
//                     console.log(response);
//                 },
//                 error: function() {
//                     console.log("BOo!");
//                     console.log(e2s.ajax_url);
//                     console.log(e2s.nonce);
//                     console.log(formData);
//                    }
//             });
//         });
//     });