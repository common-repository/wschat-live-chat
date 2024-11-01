   jQuery('.delete-conversation').on('click', async function() {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: 'You want to delete Conversation?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
      });

      if (!result.isConfirmed) {
          return;
      }

        const id = jQuery(this).data('conversation-id');

        jQuery.post(ajaxurl, {
            action: 'wschat_admin_delete_conversation',
            conversation_id: id,
            wschat_ajax_nonce: jQuery('input[name=wschat_ajax_nonce]').val()
        }, () => {
            window.location.reload();
        });
    });
