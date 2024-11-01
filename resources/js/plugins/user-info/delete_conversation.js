import { EVENTS } from '../../events';
import Swal from 'sweetalert2';

export const DeleteConversation = props => {
    const deleteConversation = async () => {

        const result = await Swal.fire({
            title: 'Are you sure?',
            text: 'Want to delete the Conversation?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
      });

      if (!result.isConfirmed) {
          return;
      }

        jQuery.post(ajaxurl, {
            action: 'wschat_admin_delete_conversation',
            conversation_id: props.chat.conversation.id,
        }, () => {
            props.chat.trigger(EVENTS.WSCHAT_ON_DELETE_CONVERSATIONS, props.chat.conversation.id);
        });
    }

    return (
        <button onClick={deleteConversation} className="btn btn-sm btn-danger mb-4 w-100">Delete Conversation</button>
    );
}
