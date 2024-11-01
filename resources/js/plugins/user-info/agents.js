import { useState, useEffect } from '@wordpress/element';
import { Tooltip } from 'bootstrap';
import Select from 'react-select';

export const AgentInfo = props => {

	const [agentOption, setSelectedAgent] = useState();
	const [participants, setParticipants] = useState([]);
	// const participants = _.pluck(props.participants, 'ID');
	//const agents = props.agents;
	const [ agents, setAgents] = useState(props.agents);
	const conversation = props.conversation;
	const chat = props.chat;
	agents.sort((a, b) => {
		if (a.status === 'online' && b.status === 'offline') {
		  return -1;
		} else if (a.status === 'offline' && b.status === 'online') {
		  return 1;
		} else {
		  return 0;
		}
	  });

	const AddAgent = () => {
        jQuery.post(wschat_ajax_obj.ajax_url, {
            action: props.chat.connector.ACTION_JOIN_CONVERSATION,
            conversation_id: props.chat.conversation.id,
            agent_id: agentOption.value
        }, () => {
        	participants.push(agentOption.value);
        	setParticipants(participants);
        	setSelectedAgent('');
    	}).fail(f => {
    		alert(f.responseJSON.data.message);
    	});
	}

	const RemoveAgent = (i) => {
		let newAgents = [...agents];
	    setAgents(newAgents);
	}

	useEffect(() => {
		setParticipants(_.pluck(props.participants, 'ID'));
		setSelectedAgent('');
	}, [props.participants])

	return (
		<div className="border-0 border-bottom border-secondary mb-2">
        	<div className="d-flex mb-2 align-items-center gap-3">
            	<h6 className="mb-0">Agents</h6>
            	<div className="d-flex">
                	{agents
                		.filter(agent => participants.indexOf(agent.ID) > -1)
                		.map((agent , i)  => <AgentAvatar key={agent.ID} agent={agent} conversation={conversation} onDelete ={ () => RemoveAgent(i)} chat={chat} participants={participants} setParticipants={setParticipants} setSelectedAgent={setSelectedAgent} /> )}
            	</div>
        	</div>

        	{props.canAddAgents ?
        	agents.filter(agent => participants.indexOf(agent.ID) === -1).length ? <div>
        	<div className="mb-2 add-agent-container">
            	<label htmlFor="">Add Another Agent</label>
            	<Select
            	    components={{ Option: CustomOption }}
            		value={agentOption}
            		onChange={selectedAgent => {setSelectedAgent(selectedAgent)}}
            		options={agents
                		.filter(agent => participants.indexOf(agent.ID) === -1)
            			.map( (agent , i) => {
            				return {label: agent.display_name, value: agent.ID, avatar: agent.avatar, status: agent.status};
            			})
            		}
            	/>
        	</div>

        	<button
        		className="btn btn-sm btn-primary mb-2 w-100 text-white"
        		onClick={AddAgent}
        		disabled={!agentOption || agents.length === 0}
        	>
        		Add Agent
        	</button>
        	</div> : <p className="text-muted">All the agents are assigned.</p> : ''}
    	</div>
	);
}

export const AgentAvatar = props => {
	const DeassignAgent = () => {
		jQuery.post(wschat_ajax_obj.ajax_url, {
			action: props.chat.connector.ACTION_DEASSIGN_AGENT,
			conversation_id: props.chat.conversation.id,
			nonce: jQuery('input[name=wschat_settings_nonce]').val(),
			agent_id: props.agent.ID
		}, () => {
			props.participants.splice(props.participants.indexOf(props.agent.ID),1);
			props.setParticipants(props.participants);
			props.setSelectedAgent('');
			props.onDelete(props.agent.ID);
		}).fail(f => {
			alert(f.responseJSON.data.message);
		});
	}
	
	useEffect(() => {
		const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
		tooltipTriggerList.forEach(el => new Tooltip(el, {
			delay: { "show": 100, "hide": 600 },
			container: document.querySelector('.wschat-wrapper'),
			trigger:'hover'
		}));
	}, [props.agent], [props.conversation]);

	return (<div
		className="elex-ws-chat-xs-profile-pic position-relative">
        <div
            className="ratio ratio-1x1 rounded-circle border border-3 border-primary overflow-hidden shadow-sm 	position-relative deassign-remove-tooltip" 		data-bs-toggle="tooltip"
			data-bs-placement="bottom"
			data-bs-html="true"
			title ={props.agent.display_name}
			data-bs-custom-class="tooltip-primary">
            <img
                src={props.agent.avatar}
            />
			
        </div>
		<span className="my-icon elex-ws-chat-profile-deassign position-absolute top-0 end-0" onClick={DeassignAgent}>&#x2715;</span>
    </div>)
};

const CustomOption = ({ innerRef, innerProps, isDisabled, data }) =>
  !isDisabled ? (
      <div {...innerProps} ref={innerRef}>
      	  <li className="list-group-item p-1 border-0 border-bottom border-secondary rounded-0 d-flex gap-2 align-items-center">
            <div className="ratio ratio-1x1 elex-ws-chat-xs-profile-pic rounded-circle overflow-hidden">
                <img src={data.avatar} alt="" />
            </div>
            <div className="flex-fill elex-ws-chat-agents-name">{data.label}</div>
            <div className="">
                <AgentStatus status={data.status} />
            </div>
        </li>
      </div>
  ) : null;

const AgentStatus = (props) =>  {
    return (<>
        {props.status === 'online' ? <span className="badge p-2 bg-success   rounded-circle">
            <span className="visually-hidden">Online</span>
        </span>: ''}

        {props.status === 'busy' ? <span className="badge p-2 bg-warning  rounded-circle">
            <span className="visually-hidden">Busy</span>
        </span>: ''}

        {props.status === 'offline' ? <span className="badge p-2 bg-secondary   rounded-circle">
            <span className="visually-hidden">Offline</span>
        </span>: '' }
    </>);

}
