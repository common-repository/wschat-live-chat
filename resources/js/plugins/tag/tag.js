import {useState, useEffect} from '@wordpress/element';
import { UnTag } from './untag';
import { CreateTag } from './create';
import { TagItem } from './tag_item';

export const Tag = props => {
    const [selected_tag, setSelectedTag] = useState();
    const [tags, setTags] = useState([]);
    const [search, setSearch] = useState('');

    const onCreate = tag => {
        props.chat.options.tags.push(tag);
        setSearch('');
        onAssign(tag);
    };

    const onUntag = () => {
        setSelectedTag(undefined);
        props.chat.$el.find('.message-item[data-message-id=' + props.message.id+']').find('.show-tags svg path').attr('stroke', "#707070"  );
    }

    const onAssign = tag => {
    	if (selected_tag && tag.id == selected_tag.id) {
    		return;
    	}

		const data = {
			message_id : props.message.id,
			tag_id : tag.id,
			action: 'wschat_admin_tag_a_message',
		};


		jQuery.post(ajaxurl, data, () => {
            setSelectedTag(tag);
            props.chat.$el.find('.message-item[data-message-id=' + props.message.id+']').find('.show-tags svg path').attr('stroke', "#" + tag.color);
            jQuery('.tags-list').addClass('visually-hidden');
		});
    }

    useEffect(() => {
        let tag = selected_tag;

        if (!selected_tag) {
            const tag_id = props.message.body.tag;
            tag = props.chat.options.tags.find(t => t.id == tag_id);
        }

        const filtered_tags = props.chat.options.tags.filter(t => {
			if (search.length === 0) {
				return true;
			}

        	return t.name.toLowerCase().indexOf(search.toLowerCase()) > -1;

        });

        setTags(filtered_tags);

    }, [search, selected_tag]);

    useEffect(() => {
        const tag_id = props.message.body.tag;
        const tag = props.chat.options.tags.find(t => t.id == tag_id);
        setSelectedTag(tag);
    }, []);


    return (
        <div className="shadow-sm p-1 rounded-3 bg-white tag-list-container">
            {selected_tag ? <UnTag tag={selected_tag} message={props.message} onUnTag={onUntag}/> : ''}
            <div className="input-group input-group-sm">
                <input type="text" value={search} onChange={e => setSearch(e.target.value)} className="form-control" placeholder="Search Tag" />
            </div>
            <div className="py-2">
                {tags.slice(0, 4).map(tag_item => (<TagItem key={tag_item.id} tag={tag_item} message={props.message} onAssign={onAssign} />))}
            </div>
            {tags.length > 4 ? <button className="btn btn-sm text-primary w-100">{tags.length - 4} More</button> : ''}
            { search != '' && tags.length === 0 && (!selected_tag || selected_tag.name != search) ? <CreateTag key={props.message.id} search={search} onCreate={onCreate}/> : ''}
        </div>
    );
}

