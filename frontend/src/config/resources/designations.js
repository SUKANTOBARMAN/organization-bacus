export default {
  key: 'designations', path: 'designations', endpoint: 'designations',
  title: 'পদবি ও র‍্যাংক', subtitle: 'র‍্যাঙ্ক যত ছোট, সদস্য ও কমিটির তালিকায় তত উপরে (সভাপতি = ১০)',
  columns: [
    { label: 'র‍্যাংক', key: 'rank' }, { label: 'পদবি', key: 'name' },
    { label: 'বাংলা', key: 'name_bn' }, { label: 'কার্যকরী কমিটি', key: 'is_executive', type: 'bool' },
  ],
  fields: [
    { name: 'name', label: 'পদবি (ইংরেজি)', required: true },
    { name: 'name_bn', label: 'পদবি (বাংলা)' },
    { name: 'rank', label: 'র‍্যাংক (১–৯৯৯৯)', type: 'number', default: 100, required: true, help: 'ছোট সংখ্যা = আগে দেখাবে' },
    { name: 'is_executive', label: 'কার্যকরী কমিটির পদ', type: 'checkbox' },
  ],
}