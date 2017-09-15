function makenewrowkey () {
	var newkey = new Date().getTime();
	return(newkey);
}
function newblankrow (index) {
	return({
		'field_id': '',
		'index': index,
		'group': "default",
		'label': "",
		'reference': "",
		'mandatory': 0,
		'type': "textfield",
		'options': "",
		'value': "",			
		'readonly': 0,
		'sortorder': 0,
		'minimizebox': 0,
		'deleted': 0 
	});
}

if (typeof start_fielddata === "undefined") {
	// console.log("Setting up blank data");
	var fielddata = [];
	var newkey = makenewrowkey();
	fielddata[newkey] = newblankrow(newkey);	
} else {
	// console.log("Using preloaded data");
	fielddata = start_fielddata;
	start_fielddata = null;
}

class DrawOneRow extends React.Component {

	constructor(props) {
	    super(props);
	    var minimizeboxonload = 0;
		if (fielddata[this.props.row.index]['minimizebox']) {
	    	minimizeboxonload = 1;
	    }
	    fielddata[this.props.row.index]['sortorder'] = this.props.rowi;

		this.state = {						
			lastchange: 	new Date().getTime(),
			minimizebox: 	minimizeboxonload,
	    };

	    
	    this.handleChangeType = this.handleChangeType.bind(this);
	    this.handleChangeLabel = this.handleChangeLabel.bind(this);
	    this.handleChangeReference = this.handleChangeReference.bind(this);
	    this.handleBlurlabel = this.handleBlurlabel.bind(this);
		this.handleChangeValue = this.handleChangeValue.bind(this);
		this.handleChangeMandatory = this.handleChangeMandatory.bind(this);
		this.handleChangeReadonly = this.handleChangeReadonly.bind(this);
		this.handleClickMinimize = this.handleClickMinimize.bind(this);
		this.handleChangeOptions = this.handleChangeOptions.bind(this);
		this.MaybeDrawOptions = this.MaybeDrawOptions.bind(this);

	}
	loglastchange () {
		this.setState({
	    	lastchange: new Date().getTime()
	    });
	}

	handleBlurlabel (e) {
		if (this.props.row.reference == "") {	    	
			var clean = cleanStr(fielddata[this.props.row.index]['label']);	
			fielddata[this.props.row.index]['reference'] = clean;	    					
			this.loglastchange ();
	    }
	}

	handleChangeType (e) {
		this.loglastchange ();
	   	fielddata[this.props.row.index]['type'] = e.target.value;
	}
	handleChangeLabel (e) {		
		this.loglastchange ();
		fielddata[this.props.row.index]['label'] = e.target.value;
	}
	handleChangeReference (e) {
		this.loglastchange ();
		fielddata[this.props.row.index]['reference'] = e.target.value;
	}
	handleChangeValue (e) {
		this.loglastchange ();
		fielddata[this.props.row.index]['value'] = e.target.value;
	}
	handleChangeMandatory (e) {
		this.loglastchange ();
		fielddata[this.props.row.index]['mandatory'] = e.target.checked;
	}
	handleChangeReadonly (e) {
		this.loglastchange ();
		fielddata[this.props.row.index]['readonly'] = e.target.checked;
	}
	handleChangeOptions (e) {
		this.loglastchange ();
		fielddata[this.props.row.index]['options'] = e.target.value;
	}
	handleClickMinimize (e) {
		e.preventDefault();
		var setthisto;	
		if (this.state.minimizebox == 1) {
			setthisto = 0;
		} else {
			setthisto = 1
		}
		// console.log('minimizebox: '+setthisto);
		fielddata[this.props.row.index]['minimizebox'] = setthisto;
		this.setState({
	    	minimizebox: setthisto
	    });
	}
	
	
	MaybeDrawOptions () {
		var needoptions = ["select", "ref_select", "checkrefset", "radioset", "multitextbox", "multiselect"];	
		var name_options 	= "options["+this.props.index+"]";

		if (needoptions.indexOf(this.props.row.type) >= 0) {
			return (<div className="form-row">
						<label>Options</label>
						<textarea value={this.props.row.options.replace(/\\n/g, "\n")} name={name_options} onChange={this.handleChangeOptions}></textarea>
					</div>);
		} else {
			return (<input type="hidden" name={name_options}/>);
		}
	}
	render () {
		if (typeof types_field !== "undefined") {
			if (this.props.row.deleted) {
				var name_deleted  	= "deleted["+this.props.index+"]";
				return(
					<div><input type="hidden" name={name_deleted} value={this.props.row.field_id}/></div>
				);

			} else {
				var indexval = "fieldrow-"+this.props.index;		
				var mTypes = types_field.map(function (item, index) {
					return <option key={index} value={item}>{item}</option>
				});
				var minimizetoggletext;
				var rowclassname = "fieldsetinner";

				if (this.state.minimizebox == 1)  {
					minimizetoggletext = "+";
					rowclassname += " minimized";
				} else {
					minimizetoggletext = "-";
					rowclassname += " maximised";
				}

				

				var name_label 		= "label["+this.props.index+"]";
				var name_ref 		= "ref["+this.props.index+"]";
				var name_value 		= "value["+this.props.index+"]";
				var name_type 		= "type["+this.props.index+"]";			
				var name_mandatory 	= "mandatory["+this.props.index+"]";
				var name_readonly 	= "readonly["+this.props.index+"]";
				var name_group 		= "group["+this.props.index+"]";
				var name_field_id 	= "field_id["+this.props.index+"]";
				var name_sort_order = "sortorder["+this.props.index+"]";
				var removeconfirm = "Remove: "+this.props.row.label;

				return(

					<div className={rowclassname} id={indexval}>
						<p className="topbar">
							<span className="boxtitle" data-title={this.props.row.label}>{this.props.row.label}</span> 
							<a href="#" onClick={this.handleClickMinimize}>{minimizetoggletext}</a>
							<a href="#" onClick={this.props.remove} data-indexval={this.props.row.index}>x</a>
							<a href="#" onClick={this.props.move} data-movedirection="up" data-indexval={this.props.index}>&uArr;</a>
							<a href="#" onClick={this.props.move} data-movedirection="down" data-indexval={this.props.index}>&dArr;</a>
						</p>
						<div className="form-row form-row-label">
							<label>Label</label>
							<input type="textfield" value={this.props.row.label} name={name_label} onBlur={this.handleBlurlabel} onChange={this.handleChangeLabel}/>						
						</div>
						<div className="form-row form-row-reference">
							<label>Reference</label>
							<input type="textfield" value={this.props.row.reference} name={name_ref} onChange={this.handleChangeReference}/>
						</div>
						<div className="form-row form-row-value">
							<label>Default Value</label>
							<input type="textfield" value={this.props.row.value} name={name_value} onChange={this.handleChangeValue}/>
						</div>
						<div className="form-row form-row-type">
							<label>Type of field</label>
							<select name={name_type} value={this.props.row.type} onChange={this.handleChangeType}>{mTypes}</select>
						</div>
						<this.MaybeDrawOptions/>
						<div className="form-row form-row-mandatory">
							<label>Mandatory Field</label>
							<input type="checkbox" checked={this.props.row.mandatory} name={name_mandatory} onChange={this.handleChangeMandatory}/>
						</div>
						<div className="form-row form-row-readonly">
							<label>Read Only</label>
							<input type="checkbox" checked={this.props.row.readonly}  name={name_readonly} onChange={this.handleChangeReadonly}/>
						</div>
						<div className="form-row form-row-group">
							<label>Group</label>
							<input type="textfield" value={this.props.row.group} name={name_group} onChange={this.handleChangeLabel}/>						
						</div>

						<input type="hidden" value={this.props.row.sortorder} name={name_sort_order}/>						
						<input type="hidden" value={this.props.row.field_id} name={name_field_id}/>						
						<input type="hidden" name="field_id[]" value={this.props.row.field_id}/>

						
					</div>
				);
			}
		} else {
			return(<div>Error loading field types</div>);
		}
	}
}

class DrawAllRows extends React.Component {

	
	render () {
		// console.log(fielddata);

		var rows = [];
		var i = 0;
		for (var k in fielddata){
		    rows.push(<DrawOneRow rowi={i} index={k} row={fielddata[k]} remove={this.props.remove} move={this.props.move}/>);    
		    i++;
		}
		if (i > 0) {
			return (<div>{rows}</div>);
		} else {
			console.log(fielddata);
			return (<div>No form rows...</div>);

		}

	}
	
}


class FieldRowContainer extends React.Component {
	 constructor(props) {
	    
	    super(props);
	    this.state = {
	    	rows: fielddata
	    };

	   this.handleClick = this.handleClick.bind(this);
	   this.handleClickRemove = this.handleClickRemove.bind(this);
	   this.handleClickMove = this.handleClickMove.bind(this);
	}	


	handleClickRemove (e) {
		e.preventDefault();
		// console.log('handleClickRemove');
		if (confirm("Remove?")) {
			//delete fielddata[e.target.dataset.indexval];		
			fielddata[e.target.dataset.indexval]['deleted'] = 1;
			// console.log(fielddata);
			this.setState({
		    	rows: fielddata
		    });
		}
	}
	handleClickMove (e) {
		e.preventDefault();
		// console.log('handleClickMove - '+e.target.dataset.movedirection+" - "+e.target.dataset.indexval);
		// console.log(e.target.dataset.indexval);
		// console.log(e.target.dataset.movedirection);

		var currentorder = [];
		var currentposition = -1;
		var newposition = -1;
		var x = 0;
		for (var k in fielddata){
			currentorder.push(k);			
			if (k == e.target.dataset.indexval) {
				currentposition = x;
			}
			x++;
		}
		x--;
		if ((e.target.dataset.movedirection == "up") && ((currentposition+1) >= 0)) {newposition = currentposition - 1;} 
		else if ((e.target.dataset.movedirection == "down") && ((currentposition+1) <= x)) {newposition = currentposition + 1;} 
		else  {
			// console.log("Nothing to change");
			return false;
		}
		// console.log("current position = "+currentposition);
		// console.log("new position = "+newposition);
		var neworder = [];
		x = 0;
		for (var k in fielddata){
			// console.log(x+" | "+k+" | "+e.target.dataset.indexval);
			if (x == newposition) {
				if (e.target.dataset.movedirection == "down") {
					neworder.push(k);	
				}
				neworder.push(e.target.dataset.indexval);
				if (e.target.dataset.movedirection == "up") {
					neworder.push(k);	
				}
				
			}
			if ((k != e.target.dataset.indexval) && (k != currentorder[newposition])) {
				neworder.push(k);	
			}
			x++;
		}

		// console.log(currentorder);
		// console.log(neworder);
		var newdata = [];
		x = 0;
		for (var k in neworder) {
			newdata[neworder[k]] = fielddata[neworder[k]];
			newdata[neworder[k]]['sortorder'] = x;
			x++;
		}

		// console.log(newdata);
		fielddata = newdata;
		this.setState({
	    	rows: fielddata
	    });
	    // console.log("Done moving");
	}
	changeData () {
		this.setState({
	    	rows: fielddata
	    });
	}
	handleClick(e) {
	    e.preventDefault();
	    
	    var newkey = makenewrowkey();
		fielddata[newkey] = newblankrow(newkey);
		
	    this.setState({
	    	rows: fielddata
	    });
	  
	}

	render () {
		return (
			<div id="field-row-container" className="gesform">
			<DrawAllRows move={this.handleClickMove} remove={this.handleClickRemove}/>
			
			<a className="button add-new-row" href="#" onClick={this.handleClick}>
		      Add Row
		    </a>

		    </div>
		)
	}
}

ReactDOM.render(
  <FieldRowContainer />,

  document.getElementById('form-react-root')
);
